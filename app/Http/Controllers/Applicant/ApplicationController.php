<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationStoreRequest;
use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Program;
use App\Models\Specialty;
use App\Services\RankingExportService;
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

        if (! $applicant->isProfileComplete()) {
            return view('applicant.application-create', [
                'limitReached' => false,
                'profileIncomplete' => true,
                'programs' => collect(),
                'applicant' => $applicant,
            ]);
        }

        // Проверка лимита 5 активных заявлений
        $activeCount = $applicant->applications()->active()->count();
        if ($activeCount >= 5) {
            return view('applicant.application-create', [
                'limitReached' => true,
                'profileIncomplete' => false,
                'programs' => collect(),
                'applicant' => $applicant,
            ]);
        }

        // Показываем только самые свежие программы для каждой специальности.
        $programs = Program::with('specialty')
            ->where('is_open', true)
            ->get()
            ->groupBy('specialty_id')
            ->map(fn($group) => $group->sortByDesc('campaign_year')->first());
        $specialtiesWithoutPrograms = Specialty::doesntHave('programs')->get();
        $occupiedPriorities = $applicant->applications()->active()->pluck('priority')->all();
        $maxSelectablePriority = min($activeCount + 1, 5);

        return view('applicant.application-create', [
            'limitReached' => false,
            'profileIncomplete' => false,
            'programs' => $programs,
            'specialtiesWithoutPrograms' => $specialtiesWithoutPrograms,
            'occupiedPriorities' => $occupiedPriorities,
            'maxSelectablePriority' => $maxSelectablePriority,
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

        if (! $applicant->isProfileComplete()) {
            return redirect()->route('applicant.profile')
                ->withErrors(['profile' => 'Заполните профиль абитуриента перед подачей заявления.']);
        }

        // Повторная проверка лимита
        $activeCount = $applicant->applications()->active()->count();
        if ($activeCount >= 5) {
            return back()->withErrors(['limit' => 'Достигнут лимит в 5 активных заявлений.']);
        }

        // Проверка, что программа открыта
        $program = Program::findOrFail($validated['program_id']);
        if (! $program->is_open) {
            return back()->withErrors(['program_id' => 'Программа не принимает заявления.']);
        }

        if ($validated['funding_type'] === 'budget' && $program->plan_count <= 0) {
            return back()->withErrors(['funding_type' => 'Для выбранной программы нет бюджетных мест.']);
        }

        if ($validated['funding_type'] === 'paid' && $program->plan_count_paid <= 0) {
            return back()->withErrors(['funding_type' => 'Для выбранной программы нет мест на хозрасчёт.']);
        }

        // Проверка на дубликат: та же специальность и тип финансирования.
        $existingDuplicate = $applicant->applications()
            ->where('status', '!=', 'cancelled')
            ->whereHas('program', function ($query) use ($program) {
                $query->where('specialty_id', $program->specialty_id);
            })
            ->where('study_form', 'full_time')
            ->where('funding_type', $validated['funding_type'])
            ->exists();

        if ($existingDuplicate) {
            return back()->withErrors(['program_id' => 'У вас уже есть активное заявление или черновик на эту специальность с таким же типом финансирования.']);
        }

        $application = DB::transaction(function () use ($applicant, $validated, $program, $request): Application {
            $application = new Application;
            $application->applicant_id = $applicant->id;
            $application->program_id = $program->id;
            $application->priority = $validated['priority'];
            $application->status = 'submitted';
            $application->revision = 1;
            $application->doc_type = $validated['doc_type'];
            $application->study_form = 'full_time';
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
            $this->insertApplicationPriority($applicant, $application, (int) $validated['priority']);

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
        $programs = Program::with('specialty')->where('is_open', true)->get();
        $occupiedPriorities = auth()->user()->applicant->applications()
            ->active()
            ->whereKeyNot($application->id)
            ->pluck('priority')
            ->all();

        return view('applicant.application-edit', compact('application', 'programs', 'occupiedPriorities'));
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
        $program = Program::findOrFail($validated['program_id']);

        // Проверка на дубликат при обновлении
        $existingDuplicate = $applicant->applications()
            ->whereKeyNot($application->id)
            ->where('status', '!=', 'cancelled')
            ->whereHas('program', function ($query) use ($program) {
                $query->where('specialty_id', $program->specialty_id);
            })
            ->where('study_form', 'full_time')
            ->where('funding_type', $validated['funding_type'])
            ->exists();

        if ($existingDuplicate) {
            return back()->withErrors(['program_id' => 'У вас уже есть другое активное заявление или черновик на эту специальность с таким же типом финансирования.']);
        }

        DB::transaction(function () use ($application, $validated, $applicant, $request): void {
            $application->priority = $validated['priority'];
            $application->doc_type = $validated['doc_type'];
            $application->study_form = 'full_time';
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
            $application->save();
            $this->insertApplicationPriority($applicant, $application, (int) $validated['priority']);

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
    public function downloadTemplate(Application $application)
    {
        $this->authorizeApplicant($application);
        $application->load('program.specialty', 'scores');

        $templatePath = base_path('templates/правила приема, заявление.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Шаблон не найден');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $specialty = $application->program->specialty;

        $templateProcessor->setValue('last_name', $application->app_last_name ?? '');
        $templateProcessor->setValue('first_name', $application->app_first_name ?? '');
        $templateProcessor->setValue('middle_name', $application->app_middle_name ?? '');
        
        $birthDate = $application->app_birth_date ? $application->app_birth_date->format('d.m.Y') : '';
        $templateProcessor->setValue('birth_date', $birthDate);
        
        $templateProcessor->setValue('document_type', 'Паспорт гражданина РФ');
        $templateProcessor->setValue('passport_series', $application->app_passport_series ?? '');
        $templateProcessor->setValue('passport_number', $application->app_passport_number ?? '');
        $templateProcessor->setValue('passport_issued_by', $application->app_passport_issued_by ?? '');
        $templateProcessor->setValue('snils', $application->app_snils ?? '');
        
        if ($specialty && $specialty->is_profession) {
            $templateProcessor->setValue('is_profession', '☑');
            $templateProcessor->setValue('is_specialnost', '☐');
            $templateProcessor->setValue('profession_code', $specialty->code ?? '');
            $templateProcessor->setValue('profession_name', $specialty->name ?? '');
            $templateProcessor->setValue('speciality_code', '');
            $templateProcessor->setValue('speciality_name', '');
        } else {
            $templateProcessor->setValue('is_profession', '☐');
            $templateProcessor->setValue('is_specialnost', '☑');
            $templateProcessor->setValue('profession_code', '');
            $templateProcessor->setValue('profession_name', '');
            $templateProcessor->setValue('speciality_code', $specialty->code ?? '');
            $templateProcessor->setValue('speciality_name', $specialty->name ?? '');
        }
        
        $templateProcessor->setValue('is_ochnaya', '☑');
        $templateProcessor->setValue('is_zaochnaya', '☐');
        
        $templateProcessor->setValue('is_budget', $application->funding_type === 'budget' ? '☑' : '☐');
        $templateProcessor->setValue('is_platno', $application->funding_type === 'paid' ? '☑' : '☐');
        
        $prevEdu = $application->app_prev_education;
        $templateProcessor->setValue('is_osnovnoe_objee_obrazovanie', $prevEdu === '9class' ? '☑' : '☐');
        $templateProcessor->setValue('is_srednee_objee_obrazovanie', $prevEdu === '11class' ? '☑' : '☐');
        $templateProcessor->setValue('is_srednee_profesionalnoe_obrazovanie', $prevEdu === 'spo' ? '☑' : '☐');
        $templateProcessor->setValue('is_vishee_obrazovanie', $prevEdu === 'vo' ? '☑' : '☐');
        
        $templateProcessor->setValue('edu_doc_series', $application->app_edu_doc_series ?? '');
        $templateProcessor->setValue('edu_doc_number', $application->app_edu_doc_number ?? '');
        
        $eduIssueDate = $application->app_edu_issue_date ? $application->app_edu_issue_date->format('d.m.Y') : '';
        $templateProcessor->setValue('edu_issue_date', $eduIssueDate);
        $templateProcessor->setValue('edu_doc_issued_by', $application->app_edu_doc_issued_by ?? '');
        
        $templateProcessor->setValue('is_have_lgota', $application->is_benefit ? '☑' : '☐');
        $templateProcessor->setValue('is_needed_v_objejitii', $application->needs_dorm ? '☑' : '☐');
        $templateProcessor->setValue('is_not_needed_v_objejitii', !$application->needs_dorm ? '☑' : '☐');
        $templateProcessor->setValue(' is_not_needed_v_objejitii ', !$application->needs_dorm ? '☑' : '☐');
        
        $templateProcessor->setValue('is_first_education', $application->is_first_spo ? '☑' : '☐');
        $templateProcessor->setValue('is_not_first_education', !$application->is_first_spo ? '☑' : '☐');

        $fileName = "zayavlenie_{$application->id}.docx";
        $tempPath = storage_path('app/public/' . $fileName);
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function downloadRanking(Application $application, RankingExportService $rankingExportService)
    {
        $this->authorizeApplicant($application);
        $application->load('program.specialty');

        $path = $rankingExportService->make($application->program, $application->funding_type);
        $fileName = 'Ранжирование_'.$application->program->specialty->code.'_'.$application->funding_type.'.xlsx';

        return response()->download($path, $fileName)->deleteFileAfterSend(true);
    }

    public function downloadDraftTemplate(Request $request)
    {
        $applicant = auth()->user()->applicant;
        $templatePath = base_path('templates/правила приема, заявление.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Шаблон не найден');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $program = \App\Models\Program::with('specialty')->find($request->input('program_id'));
        $specialty = $program ? $program->specialty : null;

        $templateProcessor->setValue('last_name', $applicant->last_name ?? '');
        $templateProcessor->setValue('first_name', $applicant->first_name ?? '');
        $templateProcessor->setValue('middle_name', $applicant->middle_name ?? '');
        
        $birthDate = $applicant->birth_date ? $applicant->birth_date->format('d.m.Y') : '';
        $templateProcessor->setValue('birth_date', $birthDate);
        
        $templateProcessor->setValue('document_type', 'Паспорт гражданина РФ');
        $templateProcessor->setValue('passport_series', $applicant->passport_series ?? '');
        $templateProcessor->setValue('passport_number', $applicant->passport_number ?? '');
        $templateProcessor->setValue('passport_issued_by', $applicant->passport_issued_by ?? '');
        $templateProcessor->setValue('snils', $applicant->snils ?? '');
        
        if ($specialty && $specialty->is_profession) {
            $templateProcessor->setValue('is_profession', '☑');
            $templateProcessor->setValue('is_specialnost', '☐');
            $templateProcessor->setValue('profession_code', $specialty->code ?? '');
            $templateProcessor->setValue('profession_name', $specialty->name ?? '');
            $templateProcessor->setValue('speciality_code', '');
            $templateProcessor->setValue('speciality_name', '');
        } else {
            $templateProcessor->setValue('is_profession', '☐');
            $templateProcessor->setValue('is_specialnost', '☑');
            $templateProcessor->setValue('profession_code', '');
            $templateProcessor->setValue('profession_name', '');
            $templateProcessor->setValue('speciality_code', $specialty->code ?? '');
            $templateProcessor->setValue('speciality_name', $specialty->name ?? '');
        }
        
        $templateProcessor->setValue('is_ochnaya', '☑');
        $templateProcessor->setValue('is_zaochnaya', '☐');
        
        $fundingType = $request->input('funding_type');
        $templateProcessor->setValue('is_budget', $fundingType === 'budget' ? '☑' : '☐');
        $templateProcessor->setValue('is_platno', $fundingType === 'paid' ? '☑' : '☐');
        
        $prevEdu = $applicant->prev_education;
        $templateProcessor->setValue('is_osnovnoe_objee_obrazovanie', $prevEdu === '9class' ? '☑' : '☐');
        $templateProcessor->setValue('is_srednee_objee_obrazovanie', $prevEdu === '11class' ? '☑' : '☐');
        $templateProcessor->setValue('is_srednee_profesionalnoe_obrazovanie', $prevEdu === 'spo' ? '☑' : '☐');
        $templateProcessor->setValue('is_vishee_obrazovanie', $prevEdu === 'vo' ? '☑' : '☐');
        
        $templateProcessor->setValue('edu_doc_series', $applicant->edu_doc_series ?? '');
        $templateProcessor->setValue('edu_doc_number', $applicant->edu_doc_number ?? '');
        
        $eduIssueDate = $applicant->edu_issue_date ? $applicant->edu_issue_date->format('d.m.Y') : '';
        $templateProcessor->setValue('edu_issue_date', $eduIssueDate);
        $templateProcessor->setValue('edu_doc_issued_by', $applicant->edu_doc_issued_by ?? '');
        
        $isBenefit = $request->boolean('is_benefit');
        $templateProcessor->setValue('is_have_lgota', $isBenefit ? '☑' : '☐');
        
        $needsDorm = $request->boolean('needs_dorm');
        $templateProcessor->setValue('is_needed_v_objejitii', $needsDorm ? '☑' : '☐');
        $templateProcessor->setValue('is_not_needed_v_objejitii', !$needsDorm ? '☑' : '☐');
        $templateProcessor->setValue(' is_not_needed_v_objejitii ', !$needsDorm ? '☑' : '☐');
        
        $isFirstSpo = $request->boolean('is_first_spo');
        $templateProcessor->setValue('is_first_education', $isFirstSpo ? '☑' : '☐');
        $templateProcessor->setValue('is_not_first_education', !$isFirstSpo ? '☑' : '☐');

        $fileName = "zayavlenie_draft_" . auth()->id() . ".docx";
        $tempPath = storage_path('app/public/' . $fileName);
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function downloadEmptyTemplate()
    {
        $templatePath = base_path('templates/правила приема, заявление пустое.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Пустой шаблон не найден');
        }

        return response()->download($templatePath, 'Заявление (пустой бланк).docx');
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

    /**
     * Вставляет заявление в выбранный приоритет и сдвигает занятые ниже.
     */
    private function insertApplicationPriority($applicant, Application $target, int $desiredPriority): void
    {
        $desiredPriority = max(1, min(5, $desiredPriority));

        $otherApplications = $applicant->applications()
            ->active()
            ->whereKeyNot($target->id)
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        foreach ($otherApplications->values() as $index => $application) {
            $normalizedPriority = $index + 1;
            $newPriority = $normalizedPriority >= $desiredPriority
                ? $normalizedPriority + 1
                : $normalizedPriority;

            if ($application->priority !== $newPriority) {
                $application->priority = $newPriority;
                $application->save();
            }
        }

        if ($target->priority !== $desiredPriority) {
            $target->priority = $desiredPriority;
            $target->save();
        }
    }

    /**
     * Удаление заявления.
     */
    public function destroy(Application $application): RedirectResponse
    {
        $this->authorizeApplicant($application);

        if (! $application->isDeletable()) {
            return back()->withErrors(['delete' => 'Заявление не может быть удалено.']);
        }

        $application->scores()->delete();
        $application->delete();

        return redirect()->route('applicant.applications')
            ->with('success', 'Заявление удалено.');
    }
}
