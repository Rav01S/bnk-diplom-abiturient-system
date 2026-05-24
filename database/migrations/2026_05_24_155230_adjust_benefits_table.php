<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('benefits', 'boost_mode')) {
            Schema::table('benefits', function (Blueprint $table): void {
                $table->string('boost_mode', 16)->nullable()->after('gives_priority');
            });
        }

        if (Schema::hasColumn('benefits', 'boosts_cert_score')) {
            DB::table('benefits')
                ->where('boosts_cert_score', true)
                ->update(['boost_mode' => 'replace']);

            Schema::table('benefits', function (Blueprint $table): void {
                $table->dropColumn('boosts_cert_score');
            });
        }

        if (Schema::hasColumn('benefits', 'description')) {
            Schema::table('benefits', function (Blueprint $table): void {
                $table->dropColumn('description');
            });
        }
    }

    public function down(): void
    {
        // Необратимая миграция: первичная create-миграция теперь
        // создаёт benefits сразу в новой схеме.
    }
};
