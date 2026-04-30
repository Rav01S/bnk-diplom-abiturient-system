<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatsController extends Controller
{
    public function index(): View
    {
        $programs = Program::with('specialty')->get();

        $stats = $programs->map(function (Program $program) {
            $budgetApproved = Application::where('program_id', $program->id)
                ->where('funding_type', 'budget')
                ->byStatus('approved')
                ->count();
            $paidApproved = Application::where('program_id', $program->id)
                ->where('funding_type', 'paid')
                ->byStatus('approved')
                ->count();
            $totalSubmitted = Application::where('program_id', $program->id)
                ->byStatus('submitted')
                ->count();

            return [
                'program' => $program,
                'plan_budget' => $program->plan_count,
                'fact_budget' => $budgetApproved,
                'plan_paid' => $program->plan_count_paid,
                'fact_paid' => $paidApproved,
                'submitted' => $totalSubmitted,
            ];
        });

        return view('commission.stats', compact('stats'));
    }

    /**
     * Экспорт статистики план/факт в CSV.
     */
    public function export(): StreamedResponse
    {
        $programs = Program::with('specialty')->get();

        return response()->streamDownload(function () use ($programs): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Специальность', 'План (бюджет)', 'Факт (бюджет)', 'План (платно)', 'Факт (платно)', 'На проверке'], ';');

            foreach ($programs as $program) {
                $budgetApproved = Application::where('program_id', $program->id)->where('funding_type', 'budget')->byStatus('approved')->count();
                $paidApproved = Application::where('program_id', $program->id)->where('funding_type', 'paid')->byStatus('approved')->count();
                $submitted = Application::where('program_id', $program->id)->byStatus('submitted')->count();

                fputcsv($handle, [
                    $program->specialty->full_title,
                    $program->plan_count,
                    $budgetApproved,
                    $program->plan_count_paid,
                    $paidApproved,
                    $submitted,
                ], ';');
            }

            fclose($handle);
        }, 'stats_'.date('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }
}
