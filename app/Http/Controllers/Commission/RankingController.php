<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
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
        if (! $selectedProgramId || ! $programs->contains('id', $selectedProgramId)) {
            $selectedProgramId = $programs->first()?->id;
        }

        $fundingType = $request->input('funding_type', 'budget');

        $ranking = collect();
        $selectedProgram = null;

        if ($selectedProgramId) {
            $selectedProgram = $programs->firstWhere('id', $selectedProgramId);

            $ranking = Application::with(['scores', 'applicant'])
                ->where('program_id', $selectedProgramId)
                ->where('funding_type', $fundingType)
                ->byStatus('approved')
                ->get()
                ->sortByDesc(fn ($app) => [
                    $app->hasPriorityBenefit() ? 1 : 0,
                    (float) $app->applicant->avg_cert_score,
                    $app->average_score,
                    $app->is_benefit ? 1 : 0,
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

        $applications = Application::with(['scores', 'applicant', 'program.specialty'])
            ->where('program_id', $programId)
            ->where('funding_type', $fundingType)
            ->byStatus('approved')
            ->get()
            ->sortByDesc(fn ($app) => [
                $app->hasPriorityBenefit() ? 1 : 0,
                (float) $app->applicant->avg_cert_score,
                $app->average_score,
                $app->is_benefit ? 1 : 0,
            ])
            ->values();

        return response()->streamDownload(function () use ($applications, $programId, $fundingType): void {
            $templatePath = base_path('templates/Ранжирование.xlsx');
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Строка 1: Колледж и Дата
            $sheet->setCellValue('A1', 'ГАПОУ «Бугурусланский нефтяной колледж» г. Бугуруслана. Ранжирование на '.date('d.m.Y').' по');

            // Строка 2: Специальность и Финансирование
            $funding = $fundingType === 'budget' ? 'Бюджет' : 'Хозрасчёт';
            $program = $programId ? Program::with('specialty')->find($programId) : ($applications->first()?->program);
            if ($program) {
                $sheet->setCellValue('A2', $program->specialty->full_title.' - '.$funding);
            }

            // Строка 3: Заголовки
            $headers = ['No', 'ФИО', 'Дата рождения', 'Б/Ж', 'Х/Р', 'Ср. балл атт.', 'Балл по 3-м предм.', 'Оригинал, Копия', 'Другая специальность', 'Телефон', 'Общежитие'];
            foreach ($headers as $index => $header) {
                $column = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue($column.'3', $header);
            }

            // Стили для заголовков
            $sheet->getStyle('A1:K3')->getFont()->setBold(true);
            $sheet->getStyle('A3:K3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $row = 4;
            foreach ($applications as $index => $app) {
                $sheet->setCellValue('A'.$row, $index + 1);

                // ФИО + Льгота
                $nameWithBenefit = $app->app_full_name;
                if ($app->is_benefit && $app->benefit_type) {
                    $nameWithBenefit .= ' ('.$app->benefit_label.')';
                }
                $sheet->setCellValue('B'.$row, $nameWithBenefit);

                $sheet->setCellValue('C'.$row, $app->app_birth_date ? $app->app_birth_date->format('d.m.Y') : '');

                // Б/Ж и Х/Р
                $sheet->setCellValue('D'.$row, $app->funding_type === 'budget' ? '+' : '');
                $sheet->setCellValue('E'.$row, $app->funding_type === 'paid' ? '+' : '');

                // Средний балл аттестата (F)
                if ($app->is_benefit && $app->benefit_type === 'svo') {
                    $sheet->setCellValue('F'.$row, '6,00 ('.number_format($app->applicant->avg_cert_score ?? 0, 2, ',', '').')');
                } else {
                    $sheet->setCellValue('F'.$row, $app->applicant->avg_cert_score ? (float) $app->applicant->avg_cert_score : '');
                }

                // Средний балл по 3 предметам (G)
                $sheet->setCellValue('G'.$row, (float) $app->average_score);

                $sheet->setCellValue('H'.$row, $app->doc_type === 'original' ? 'Оригинал' : 'Копия');

                // Другие специальности
                $otherApps = Application::with('program.specialty')
                    ->where('applicant_id', $app->applicant_id)
                    ->where('id', '!=', $app->id)
                    ->get()
                    ->map(fn ($otherApp) => $otherApp->program->specialty->code)
                    ->filter()
                    ->implode(', ');
                $sheet->setCellValue('I'.$row, $otherApps);

                $sheet->setCellValue('J'.$row, $app->app_phone);
                $sheet->setCellValue('K'.$row, $app->needs_dorm ? 'Да' : 'Нет');

                // Стили и границы
                $sheet->getStyle('A'.$row.':K'.$row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);

                $row++;
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'Ранжирование_'.date('d.m.Y').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
