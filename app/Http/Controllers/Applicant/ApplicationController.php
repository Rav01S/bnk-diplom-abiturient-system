<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationStoreRequest;
use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applicant = auth()->user()->applicant;
        $query = $applicant->applications()->with('program.specialty', 'scores');

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->byStatus($request->input('status'));
        }

        // Поиск
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('program.specialty', function ($sq) use ($search): void {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        // Сортировка
        $sort = $request->input('sort', 'date_desc');
        $query = match ($sort) {
            'date_asc' => $query->oldest(),
            'priority_asc' => $query->orderBy('priority'),
            'priority_desc' => $query->orderByDesc('priority'),
            default => $query->latest(),
        };

        $applications = $query->paginate(10);

        return view('applicant.applications', compact('applications'));
    }

    public function create(): View
    {
        $applicant = auth()->user()->applicant;

        // Проверка лимита 5 активных заявлений
        $activeCount = $applicant->applications()->active()->count();
        if ($activeCount >= 5) {
            return view('applicant.application-create', [
                'limitReached' => true,
                'programs' => collect(),
                'applicant' => $applicant,
            ]);
        }

        // Открытые программы
        $programs = Program::open()->with('specialty')->get();

        return view('applicant.application-create', [
            'limitReached' => false,
            'programs' => $programs,
            'applicant' => $applicant,
        ]);
    }

    /**
     * Создание заявления: транзакция → снапшот → баллы → файл.
     */
    public function store(ApplicationStoreRequest $request): RedirectResponse
    {
        $applicant = auth()->user()->applicant;
        $validated = $request->validated();

        // Повторная проверка лимита
        $activeCount = $applicant->applications()->active()->count();
        if ($activeCount >= 5) {
            return back()->withErrors(['limit' => 'Достигнут лимит в 5 активных заявлений.']);
        }

        // Проверка, что программа открыта
        $program = Program::findOrFail($validated['program_id']);
        if (! $program->isAcceptingApplications()) {
            return back()->withErrors(['program_id' => 'Программа не принимает заявления.']);
        }

        $application = DB::transaction(function () use ($applicant, $validated, $program, $request): Application {
            $application = new Application;
            $application->applicant_id = $applicant->id;
            $application->program_id = $program->id;
            $application->priority = $validated['priority'];
            $application->status = 'submitted';
            $application->revision = 1;
            $application->doc_type = $validated['doc_type'];
            $application->study_form = $program->has_study_form ? $validated['study_form'] : 'full_time';
            $application->funding_type = $validated['funding_type'];
            $application->is_benefit = $request->boolean('is_benefit');
            $application->benefit_type = $request->input('benefit_type');
            $application->needs_dorm = $request->boolean('needs_dorm');
            $application->is_first_spo = $request->boolean('is_first_spo', true);

            // Снапшот данных профиля
            $application->fillSnapshot($applicant);

            // Подписанный документ
            if ($request->hasFile('signed_doc_photo')) {
                $application->signed_doc_photo = $request->file('signed_doc_photo')
                    ->store('uploads/signed', 'public');
            }

            $application->save();

            // Баллы по предметам
            foreach ($validated['scores'] as $scoreData) {
                ApplicationScore::create([
                    'application_id' => $application->id,
                    'subject_name' => $scoreData['subject_name'],
                    'score' => $scoreData['score'],
                ]);
            }

            return $application;
        });

        return redirect()->route('applicant.applications.show', $application)
            ->with('success', 'Заявление успешно подано!');
    }

    public function show(Application $application): View
    {
        $this->authorizeApplicant($application);
        $application->load('program.specialty', 'scores');

        return view('applicant.application-view', compact('application'));
    }

    public function edit(Application $application): View
    {
        $this->authorizeApplicant($application);

        if (! $application->isEditable()) {
            abort(403, 'Заявление не может быть отредактировано.');
        }

        $application->load('program.specialty', 'scores');
        $programs = Program::open()->with('specialty')->get();

        return view('applicant.application-edit', compact('application', 'programs'));
    }

    /**
     * Обновление заявления (доработка): revision++, статус → submitted.
     */
    public function update(ApplicationStoreRequest $request, Application $application): RedirectResponse
    {
        $this->authorizeApplicant($application);

        if (! $application->isEditable()) {
            abort(403, 'Заявление не может быть отредактировано.');
        }

        $validated = $request->validated();
        $applicant = auth()->user()->applicant;

        DB::transaction(function () use ($application, $validated, $applicant, $request): void {
            $application->priority = $validated['priority'];
            $application->doc_type = $validated['doc_type'];
            $application->study_form = $validated['study_form'];
            $application->funding_type = $validated['funding_type'];
            $application->is_benefit = $request->boolean('is_benefit');
            $application->benefit_type = $request->input('benefit_type');
            $application->needs_dorm = $request->boolean('needs_dorm');
            $application->is_first_spo = $request->boolean('is_first_spo', true);

            // Обновляем снапшот
            $application->fillSnapshot($applicant);

            // Обновляем фото
            if ($request->hasFile('signed_doc_photo')) {
                if ($application->signed_doc_photo) {
                    Storage::disk('public')->delete($application->signed_doc_photo);
                }
                $application->signed_doc_photo = $request->file('signed_doc_photo')
                    ->store('uploads/signed', 'public');
            }

            // Ревизия и статус
            $application->revision++;
            $application->status = 'submitted';
            $application->rejection_reason = null;
            $application->save();

            // Обновление баллов
            $application->scores()->delete();
            foreach ($validated['scores'] as $scoreData) {
                ApplicationScore::create([
                    'application_id' => $application->id,
                    'subject_name' => $scoreData['subject_name'],
                    'score' => $scoreData['score'],
                ]);
            }
        });

        return redirect()->route('applicant.applications.show', $application)
            ->with('success', 'Заявление обновлено и отправлено на повторную проверку.');
    }

    /**
     * Отмена заявления.
     */
    public function cancel(Application $application): RedirectResponse
    {
        $this->authorizeApplicant($application);

        if (! $application->isCancellable()) {
            return back()->withErrors(['cancel' => 'Заявление не может быть отменено.']);
        }

        $application->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->route('applicant.applications')
            ->with('success', 'Заявление отменено.');
    }

    /**
     * Скачивание текстового шаблона заявления.
     */
    public function downloadTemplate(Application $application): StreamedResponse
    {
        $this->authorizeApplicant($application);
        $application->load('program.specialty', 'scores');

        $specialty = $application->program->specialty;

        return response()->streamDownload(function () use ($application, $specialty): void {
            $content = "ЗАЯВЛЕНИЕ\n\n";
            $content .= "От абитуриента: {$application->app_full_name}\n";
            $content .= "Дата рождения: ".($application->app_birth_date?->format('d.m.Y') ?? '—')."\n";
            $content .= "Паспорт: {$application->app_passport_series} {$application->app_passport_number}\n";
            $content .= "Выдан: {$application->app_passport_issued_by}\n";
            $content .= "СНИЛС: {$application->app_snils}\n\n";
            $content .= "Прошу зачислить на специальность:\n";
            $content .= "{$specialty->full_title}\n\n";
            $content .= "Форма обучения: ".($application->study_form === 'full_time' ? 'Очная' : 'Заочная')."\n";
            $content .= "Финансирование: ".($application->funding_type === 'budget' ? 'Бюджет' : 'Платное')."\n";
            $content .= "Приоритет: {$application->priority}\n\n";
            $content .= "Оценки в аттестате:\n";
            foreach ($application->scores as $score) {
                $content .= "  {$score->subject_name}: {$score->score}\n";
            }
            $content .= "\n\nДата: ____________  Подпись: ____________\n";
            echo $content;
        }, "zayavlenie_{$application->id}.txt", [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    /**
     * Проверка что заявление принадлежит текущему абитуриенту.
     */
    private function authorizeApplicant(Application $application): void
    {
        if ($application->applicant_id !== auth()->user()->applicant->id) {
            abort(403);
        }
    }
}
