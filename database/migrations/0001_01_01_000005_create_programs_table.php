<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Программы приёма — привязаны к специальности, управляют планами и сроками.
     */
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('campaign_year');
            $table->boolean('has_study_form')->default(true);
            $table->unsignedInteger('plan_count')->default(0);
            $table->unsignedInteger('plan_count_paid')->default(0);
            $table->boolean('is_open')->default(false);
            $table->date('open_from')->nullable();
            $table->date('open_until')->nullable();
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
