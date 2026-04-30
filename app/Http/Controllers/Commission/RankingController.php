<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RankingController extends Controller
{
    public function index(Request $request): View
    {
        $programs = Program::with('specialty')->get();
        $selectedProgramId = $request->input('program_id');
        $fundingType = $request->input('funding_type', 'budget');

        $ranking = collect();
        $selectedProgram = null;

        if ($selectedProgramId) {
            $selectedProgram = Program::with('specialty')->find($selectedProgramId);

            $ranking = Application::with('scores')
                ->where('program_id', $selectedProgramId)
                ->where('funding_type', $fundingType)
                ->byStatus('approved')
                ->get()
                ->sortByDesc(fn ($app) => $app->total_score)
                ->values();
        }

        return view('commission.ranking', compact('programs', 'ranking', 'selectedProgram', 'selectedProgramId', 'fundingType'));
    }

    /**
     * Экспорт ранжирования в CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $programId = $request->input('program_id');
        $fundingType = $request->input('funding_type', 'budget');

        $applications = Application::with('scores', 'program.specialty')
            ->where('program_id', $programId)
            ->where('funding_type', $fundingType)
            ->byStatus('approved')
            ->get()
            ->sortByDesc(fn ($app) => $app->total_score)
            ->values();

        return response()->streamDownload(function () use ($applications): void {
            $handle = fopen('php://output', 'w');
            // BOM для корректного отображения кириллицы в Excel
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Место', 'ФИО', 'Балл 1', 'Балл 2', 'Балл 3', 'Сумма', 'Приоритет'], ';');

            foreach ($applications as $index => $app) {
                $scores = $app->scores->pluck('score')->toArray();
                fputcsv($handle, [
                    $index + 1,
                    $app->app_full_name,
                    $scores[0] ?? '—',
                    $scores[1] ?? '—',
                    $scores[2] ?? '—',
                    $app->total_score,
                    $app->priority,
                ], ';');
            }

            fclose($handle);
        }, 'ranking_'.date('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }
}
