<?php
require 'vendor/autoload.php';

echo "Ranking.xlsx:\n";
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('templates/Ранжирование.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();
print_r(array_slice($data, 0, 5));

echo "\nStats.xlsx:\n";
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('templates/Статистика за год на данный момент.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();
print_r(array_slice($data, 0, 5));
