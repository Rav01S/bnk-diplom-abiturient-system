<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$duplicates = DB::table('programs')
    ->select('specialty_id', 'campaign_year', DB::raw('MIN(id) as min_id'))
    ->groupBy('specialty_id', 'campaign_year')
    ->having(DB::raw('COUNT(*)'), '>', 1)
    ->get();

foreach ($duplicates as $dup) {
    $idsToDelete = DB::table('programs')
        ->where('specialty_id', $dup->specialty_id)
        ->where('campaign_year', $dup->campaign_year)
        ->where('id', '>', $dup->min_id)
        ->pluck('id');

    foreach ($idsToDelete as $id) {
        DB::table('applications')->where('program_id', $id)->update(['program_id' => $dup->min_id]);
        DB::table('programs')->where('id', $id)->delete();
    }
    echo "Merged and deleted duplicates for Specialty {$dup->specialty_id}, Year {$dup->campaign_year}\n";
}
