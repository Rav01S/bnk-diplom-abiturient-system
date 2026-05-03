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
        $year = $request->input('campaign_year', date('Y'));
        
        $programs = Program::with('specialty')
            ->where('campaign_year', $year)
            ->get();
            
        $selectedProgramId = $request->input('program_id');
        
        // Если выбранная программа не из этого года или не задана, берем первую доступную за год
        if (!$selectedProgramId || !$programs->contains('id', $selectedProgramId)) {
            $selectedProgramId = $programs->first()?->id;
        }
        
        $fundingType = $request->input('funding_type', 'budget');

        $ranking = collect();
        $selectedProgram = null;

        if ($selectedProgramId) {
            $selectedProgram = $programs->firstWhere('id', $selectedProgramId);

            $ranking = Application::with('scores')
                ->where('program_id', $selectedProgramId)
                ->where('funding_type', $fundingType)
                ->byStatus('approved')
                ->get()
                ->sortByDesc(fn ($app) => [
                    $app->is_benefit && $app->benefit_type === 'svo' ? 6.0 : (float) $app->app_avg_cert_score,
                    $app->average_score
                ])
                ->values();
        }

        return view('commission.ranking', compact('programs', 'ranking', 'selectedProgram', 'selectedProgramId', 'fundingType', 'year'));
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
            ->sortByDesc(fn ($app) => [
                $app->is_benefit && $app->benefit_type === 'svo' ? 6.0 : (float) $app->app_avg_cert_score,
                $app->average_score
            ])
            ->values();

        return response()->streamDownload(function () use ($applications, $programId): void {
            $templatePath = base_path('templates/Ранжирование.xlsx');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            if ($applications->isNotEmpty()) {
                $program = $applications->first()->program;
                $sheet->setCellValue('A1', $program->specialty->code . ' ' . $program->specialty->name);
            } else if ($programId) {
                $program = Program::with('specialty')->find($programId);
                if ($program) {
                    $sheet->setCellValue('A1', $program->specialty->code . ' ' . $program->specialty->name);
                }
            }

            $row = 3;
            foreach ($applications as $index => $app) {
                $sheet->setCellValue('A' . $row, $index + 1);
                
                // ФИО + Льгота
                $nameWithBenefit = $app->app_full_name;
                if ($app->is_benefit && $app->benefit_type) {
                    $benefitLabel = $app->benefit_type === 'olympiad' ? 'Олимпиада' : ($app->benefit_type === 'disability' ? 'Инвалидность' : ($app->benefit_type === 'svo' ? 'СВО' : $app->benefit_type));
                    $nameWithBenefit .= ' (' . $benefitLabel . ')';
                }
                $sheet->setCellValue('B' . $row, $nameWithBenefit);
                
                $sheet->setCellValue('C' . $row, $app->app_birth_date ? $app->app_birth_date->format('d.m.Y') : '');
                $sheet->setCellValue('D' . $row, $app->funding_type === 'budget' ? '+' : ''); // Б/Ж
                $sheet->setCellValue('E' . $row, $app->funding_type === 'paid' ? '+' : ''); // Х/Р
                
                // Средний балл аттестата (F)
                if ($app->is_benefit && $app->benefit_type === 'svo') {
                    $sheet->setCellValue('F' . $row, '6,00 (' . number_format($app->app_avg_cert_score, 2, ',', '') . ')');
                } else {
                    $sheet->setCellValue('F' . $row, $app->app_avg_cert_score ? number_format($app->app_avg_cert_score, 2, ',', '') : '');
                }
                
                // Средний балл по 3 предметам (G)
                $sheet->setCellValue('G' . $row, number_format($app->average_score, 2, ',', ''));
                
                $sheet->setCellValue('H' . $row, $app->doc_type === 'original' ? 'Оригинал' : 'Копия');
                
                // Другие специальности
                $otherApps = Application::with('program.specialty')
                    ->where('applicant_id', $app->applicant_id)
                    ->where('id', '!=', $app->id)
                    ->get()
                    ->map(fn($otherApp) => $otherApp->program->specialty->code)
                    ->implode(', ');
                $sheet->setCellValue('I' . $row, $otherApps);
                
                $sheet->setCellValue('J' . $row, $app->app_phone);
                $sheet->setCellValue('K' . $row, $app->needs_dorm ? 'Да' : 'Нет');
                $row++;
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'Ранжирование_'.date('Y-m-d').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
