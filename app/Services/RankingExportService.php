<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Program;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;
use ZipArchive;

class RankingExportService
{
    public function make(Program $program, string $fundingType): string
    {
        $program->loadMissing('specialty');

        $applications = $this->applications($program, $fundingType);
        $templatePath = base_path('templates/Ранжирование.xlsx');

        if (! is_file($templatePath)) {
            throw new RuntimeException('Ranking template file was not found.');
        }

        $workDir = storage_path('app/generated/ranking_'.$program->id.'_'.uniqid());
        $outputPath = storage_path('app/generated/ranking_'.$program->id.'_'.date('Y-m-d').'.xlsx');

        File::ensureDirectoryExists(dirname($outputPath));
        File::deleteDirectory($workDir);
        File::ensureDirectoryExists($workDir);

        $zip = new ZipArchive;
        if ($zip->open($templatePath) !== true) {
            throw new RuntimeException('Could not open ranking template.');
        }

        $zip->extractTo($workDir);
        $zip->close();

        $sheetPath = $workDir.DIRECTORY_SEPARATOR.'xl'.DIRECTORY_SEPARATOR.'worksheets'.DIRECTORY_SEPARATOR.'sheet1.xml';
        $sheetXml = File::get($sheetPath);
        $sheetXml = $this->replaceSheetData($sheetXml, $program, $fundingType, $applications);
        File::put($sheetPath, $sheetXml);

        File::delete($outputPath);
        $out = new ZipArchive;
        if ($out->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            File::deleteDirectory($workDir);
            throw new RuntimeException('Could not create ranking export.');
        }

        foreach (File::allFiles($workDir) as $file) {
            $out->addFile($file->getPathname(), str_replace('\\', '/', $file->getRelativePathname()));
        }

        $out->close();
        File::deleteDirectory($workDir);

        return $outputPath;
    }

    /** @return Collection<int, Application> */
    public function applications(Program $program, string $fundingType): Collection
    {
        return Application::with('scores', 'program.specialty', 'applicant')
            ->where('program_id', $program->id)
            ->where('funding_type', $fundingType)
            ->byStatus('approved')
            ->get()
            ->sortByDesc(fn (Application $application) => [
                $application->hasPriorityBenefit() ? 1 : 0,
                $application->effectiveCertScore(),
                $application->average_score,
                $application->is_benefit ? 1 : 0,
            ])
            ->values();
    }

    private function replaceSheetData(string $sheetXml, Program $program, string $fundingType, Collection $applications): string
    {
        $lastRow = max(4, $applications->count() + 3);
        $sheetData = '<sheetData>'
            .$this->titleRow()
            .$this->specialtyRow($program, $fundingType)
            .$this->headerRow()
            .$this->dataRows($applications, $fundingType)
            .'</sheetData>';

        $sheetXml = preg_replace('/<dimension ref="[^"]+"\/>/', '<dimension ref="A1:K'.$lastRow.'"/>', $sheetXml, 1);
        $sheetXml = preg_replace('/<sheetData>.*<\/sheetData>/s', $sheetData, $sheetXml, 1);

        return $sheetXml;
    }

    private function titleRow(): string
    {
        $title = 'ГАПОУ «Бугурусланский нефтяной колледж» г. Бугуруслана. Ранжирование на '.date('d.m.Y').' по';
        $cells = $this->inlineCell('A', 1, $title, 3);
        foreach (range('B', 'K') as $column) {
            $cells .= '<c r="'.$column.'1" s="3"/>';
        }

        return '<row r="1" spans="1:11" x14ac:dyDescent="0.25">'.$cells.'</row>';
    }

    private function specialtyRow(Program $program, string $fundingType): string
    {
        $funding = $fundingType === 'budget' ? 'Бюджет' : 'Хозрасчёт';
        $content = $program->specialty->full_title.' - '.$funding;
        $cells = $this->inlineCell('A', 2, $content, 3);
        foreach (range('B', 'K') as $column) {
            $cells .= '<c r="'.$column.'2" s="3"/>';
        }

        return '<row r="2" spans="1:11" x14ac:dyDescent="0.25">'.$cells.'</row>';
    }

    private function headerRow(): string
    {
        $headers = ['No', 'ФИО', 'Дата рождения', 'Б/Ж', 'Х/Р', 'Ср. балл атт.', 'Балл по 3-м предм.', 'Оригинал, Копия', 'Другая специальность', 'Телефон', 'Общежитие'];
        $cells = '';

        foreach ($headers as $index => $header) {
            $cells .= $this->inlineCell($this->columnName($index + 1), 3, $header, 1);
        }

        return '<row r="3" spans="1:11" x14ac:dyDescent="0.25">'.$cells.'</row>';
    }

    /** @param Collection<int, Application> $applications */
    private function dataRows(Collection $applications, string $fundingType): string
    {
        if ($applications->isEmpty()) {
            return $this->emptyRow(4);
        }

        return $applications
            ->map(fn (Application $application, int $index) => $this->dataRow($application, $index + 4, $index + 1, $fundingType))
            ->implode('');
    }

    private function dataRow(Application $application, int $row, int $place, string $fundingType): string
    {
        $otherSpecialties = $application->applicant
            ? $application->applicant->applications()
                ->with('program.specialty')
                ->active()
                ->whereKeyNot($application->id)
                ->get()
                ->map(fn (Application $item) => $item->program?->specialty?->full_title)
                ->filter()
                ->implode('; ')
            : '';

        $values = [
            $place,
            $application->app_full_name,
            $application->app_birth_date?->format('d.m.Y') ?? '',
            $fundingType === 'budget' ? 'Б' : 'П',
            $application->study_form === 'full_time' ? 'О' : 'З',
            ($boosted = $application->boostedCertScore()) !== null
                ? '*'.number_format($boosted, 2, ',', '').' ('.number_format($application->applicant->avg_cert_score ?? 0, 2, ',', '').')'
                : number_format($application->applicant->avg_cert_score ?? 0, 2, ',', ''),
            number_format($application->average_score, 2, ',', ''),
            $application->doc_type === 'original' ? 'Оригинал' : 'Копия',
            $otherSpecialties,
            $application->app_phone,
            $application->needs_dorm ? 'Да' : 'Нет',
        ];

        $cells = '';
        foreach ($values as $index => $value) {
            $cells .= $this->inlineCell($this->columnName($index + 1), $row, (string) $value, $index === 0 ? 2 : 1);
        }

        return '<row r="'.$row.'" spans="1:11" x14ac:dyDescent="0.25">'.$cells.'</row>';
    }

    private function emptyRow(int $row): string
    {
        $cells = '';
        foreach (range('A', 'K') as $column) {
            $cells .= '<c r="'.$column.$row.'" s="1"/>';
        }

        return '<row r="'.$row.'" spans="1:11" x14ac:dyDescent="0.25">'.$cells.'</row>';
    }

    private function inlineCell(string $column, int $row, string $value, int $style): string
    {
        return '<c r="'.$column.$row.'" s="'.$style.'" t="inlineStr"><is><t>'.$this->escape($value).'</t></is></c>';
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
