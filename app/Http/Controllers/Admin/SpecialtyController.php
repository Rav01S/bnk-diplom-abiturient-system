<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SpecialtyController extends Controller
{
    public function index(): View
    {
        $specialties = Specialty::with('programs')->get();

        return view('admin.specialties', compact('specialties'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:specialties,code'],
            'name' => ['required', 'string', 'max:255'],
            'subject_1' => ['required', 'string', 'max:100'],
            'subject_2' => ['required', 'string', 'max:100'],
            'subject_3' => ['required', 'string', 'max:100'],
            'is_profession' => ['nullable', 'boolean'],
        ]);

        $validated['is_profession'] = $request->boolean('is_profession');

        Specialty::create($validated);

        return redirect()->route('admin.specialties')->with('success', 'Специальность добавлена.');
    }

    public function update(Request $request, Specialty $specialty): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:specialties,code,'.$specialty->id],
            'name' => ['required', 'string', 'max:255'],
            'subject_1' => ['required', 'string', 'max:100'],
            'subject_2' => ['required', 'string', 'max:100'],
            'subject_3' => ['required', 'string', 'max:100'],
            'is_profession' => ['nullable', 'boolean'],
        ]);

        $validated['is_profession'] = $request->boolean('is_profession');

        $specialty->update($validated);

        return redirect()->route('admin.specialties')->with('success', 'Специальность обновлена.');
    }

    public function destroy(Specialty $specialty): RedirectResponse
    {
        // Подсчёт затронутых заявлений
        $appCount = Application::whereIn(
            'program_id',
            $specialty->programs()->pluck('id')
        )->count();

        DB::transaction(function () use ($specialty): void {
            // Удаляем заявления (scores каскадятся автоматически)
            Application::whereIn(
                'program_id',
                $specialty->programs()->pluck('id')
            )->delete();

            // Программы каскадятся из миграции
            $specialty->delete();
        });

        $msg = 'Специальность удалена.';
        if ($appCount > 0) {
            $msg .= " Также удалено заявлений: {$appCount}.";
        }

        return redirect()->route('admin.specialties')->with('success', $msg);
    }

    public function storeProgram(Request $request, Specialty $specialty): RedirectResponse
    {
        $validated = $request->validate(
            [
                'campaign_year' => [
                    'required', 'integer', 'min:2020', 'max:2030',
                    Rule::unique('programs')->where('specialty_id', $specialty->id),
                ],
                'plan_count' => ['required', 'integer', 'min:0'],
                'plan_count_paid' => ['required', 'integer', 'min:0'],
                'has_study_form' => ['required', 'boolean'],
                'is_open' => ['nullable', 'boolean'],
                'open_from' => ['nullable', 'date'],
                'open_until' => ['nullable', 'date', 'after_or_equal:open_from'],
            ],
            [
                'campaign_year.unique' => 'Программа на этот год уже создана.',
            ],
        );

        $validated['has_study_form'] = $request->boolean('has_study_form');
        $validated['is_open'] = $request->boolean('is_open');

        $specialty->programs()->create($validated);

        return redirect()->route('admin.specialties')->with('success', 'Программа добавлена.');
    }

    public function updateProgram(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate(
            [
                'campaign_year' => [
                    'required', 'integer', 'min:2020', 'max:2030',
                    Rule::unique('programs')->where('specialty_id', $program->specialty_id)->ignore($program->id),
                ],
                'plan_count' => ['required', 'integer', 'min:0'],
                'plan_count_paid' => ['required', 'integer', 'min:0'],
                'has_study_form' => ['required', 'boolean'],
                'is_open' => ['nullable', 'boolean'],
                'open_from' => ['nullable', 'date'],
                'open_until' => ['nullable', 'date', 'after_or_equal:open_from'],
            ],
            [
                'campaign_year.unique' => 'Программа на этот год уже создана.',
            ],
        );

        $validated['has_study_form'] = $request->boolean('has_study_form');
        $validated['is_open'] = $request->boolean('is_open');

        $program->update($validated);

        return redirect()->route('admin.specialties')->with('success', 'Программа обновлена.');
    }

    public function destroyProgram(Program $program): RedirectResponse
    {
        $appCount = $program->applications()->count();

        DB::transaction(function () use ($program): void {
            $program->applications()->delete();
            $program->delete();
        });

        $msg = 'Программа удалена.';
        if ($appCount > 0) {
            $msg .= " Также удалено заявлений: {$appCount}.";
        }

        return redirect()->route('admin.specialties')->with('success', $msg);
    }

    /**
     * Тоггл открытия/закрытия приёма.
     */
    public function toggleProgram(Program $program): RedirectResponse
    {
        $program->update(['is_open' => ! $program->is_open]);

        $status = $program->is_open ? 'открыт' : 'закрыт';

        return redirect()->route('admin.specialties')
            ->with('success', "Приём {$status} для программы №{$program->id}.");
    }
}
