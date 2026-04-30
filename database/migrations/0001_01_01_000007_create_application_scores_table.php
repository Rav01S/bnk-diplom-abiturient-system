<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Оценки по предметам для каждого заявления.
     */
    public function up(): void
    {
        Schema::create('application_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('subject_name');
            $table->decimal('score', 3, 1);

            $table->unique(['application_id', 'subject_name']);
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_scores');
    }
};
