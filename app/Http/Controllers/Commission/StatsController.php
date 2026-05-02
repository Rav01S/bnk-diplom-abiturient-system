<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatsController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $year = $request->input('campaign_year', date('Y'));
        $programs = Program::with('specialty')
            ->where('campaign_year', $year)
            ->get();

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

        return view('commission.stats', compact('stats', 'year'));
    }

    /**
     * Экспорт статистики план/факт в Excel.
     */
    public function export(\Illuminate\Http\Request $request): StreamedResponse
    {
        $year = $request->input('campaign_year', date('Y'));
        $programs = Program::with('specialty')
            ->where('campaign_year', $year)
            ->get();

        return response()->streamDownload(function () use ($programs): void {
            $templatePath = base_path('templates/Статистика за год на данный момент.xlsx');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $months = [
                1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
                5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
                9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
            ];
            $currentMonth = $months[(int)date('n')];
            $sheet->setCellValue('A2', 'на "' . date('d') . '" ' . $currentMonth . ' ' . date('Y') . ' года');

            $row = 5;
            foreach ($programs as $program) {
                $budgetApproved = Application::where('program_id', $program->id)->where('funding_type', 'budget')->byStatus('approved')->count();
                $paidApproved = Application::where('program_id', $program->id)->where('funding_type', 'paid')->byStatus('approved')->count();

                $sheet->setCellValue('A' . $row, $program->specialty->code);
                $sheet->setCellValue('B' . $row, $program->specialty->name);
                $sheet->setCellValue('C' . $row, $program->plan_count);
                $sheet->setCellValue('D' . $row, $budgetApproved);
                $sheet->setCellValue('E' . $row, $program->plan_count_paid);
                $sheet->setCellValue('F' . $row, $paidApproved);
                $row++;
            }

            if ($row > 5) {
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ];
                $sheet->getStyle('A5:F' . ($row - 1))->applyFromArray($styleArray);
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'статистика_'.date('Y-m-d').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
