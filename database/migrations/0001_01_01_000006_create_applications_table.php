<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Заявления — снапшот данных абитуриента + статусная модель.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->restrictOnDelete();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();

            // Параметры заявления
            $table->unsignedTinyInteger('priority')->default(1);
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'rework_needed', 'cancelled'])->default('draft');
            $table->unsignedInteger('revision')->default(1);
            $table->enum('doc_type', ['original', 'copy'])->default('original');
            $table->enum('study_form', ['full_time', 'part_time'])->default('full_time');
            $table->enum('funding_type', ['budget', 'paid'])->default('budget');
            $table->boolean('is_benefit')->default(false);
            $table->string('benefit_type')->nullable();
            $table->boolean('needs_dorm')->default(false);
            $table->boolean('is_first_spo')->default(true);

            // Подписанный документ и причина отклонения
            $table->string('signed_doc_photo')->nullable();
            $table->text('rejection_reason')->nullable();

            // Снапшот данных абитуриента на момент подачи
            $table->string('app_last_name')->nullable();
            $table->string('app_first_name')->nullable();
            $table->string('app_middle_name')->nullable();
            $table->date('app_birth_date')->nullable();
            $table->string('app_passport_series', 4)->nullable();
            $table->string('app_passport_number', 6)->nullable();
            $table->string('app_passport_issued_by')->nullable();
            $table->string('app_snils', 14)->nullable();
            $table->string('app_prev_education')->nullable();
            $table->string('app_edu_doc_series', 10)->nullable();
            $table->string('app_edu_doc_number', 20)->nullable();
            $table->string('app_edu_doc_issued_by')->nullable();
            $table->date('app_edu_issue_date')->nullable();
            $table->decimal('app_avg_cert_score', 3, 2)->nullable();
            $table->string('app_phone', 20)->nullable();
            $table->string('app_photo_passport')->nullable();
            $table->string('app_photo_snils')->nullable();
            $table->string('app_photo_edu_1')->nullable();
            $table->string('app_photo_edu_2')->nullable();
            $table->string('app_photo_edu_3')->nullable();

            $table->timestamps();
            $table->timestamp('cancelled_at')->nullable();
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
