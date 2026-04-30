<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Профиль абитуриента — личные данные и документы.
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // ФИО
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->date('birth_date')->nullable();

            // Паспорт
            $table->string('passport_series', 4)->nullable();
            $table->string('passport_number', 6)->nullable();
            $table->string('passport_issued_by')->nullable();

            // СНИЛС, телефон
            $table->string('snils', 14)->nullable();
            $table->string('phone', 20)->nullable();

            // Образование
            $table->string('prev_education')->nullable();
            $table->string('edu_doc_series', 10)->nullable();
            $table->string('edu_doc_number', 20)->nullable();
            $table->string('edu_doc_issued_by')->nullable();
            $table->date('edu_issue_date')->nullable();
            $table->decimal('avg_cert_score', 3, 2)->nullable();

            // Фото документов (относительные пути)
            $table->string('photo_passport')->nullable();
            $table->string('photo_snils')->nullable();
            $table->string('photo_edu_1')->nullable();
            $table->string('photo_edu_2')->nullable();
            $table->string('photo_edu_3')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
