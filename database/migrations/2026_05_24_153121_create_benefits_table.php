<?php

use App\Models\Application;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Справочник льгот и правил их влияния на ранжирование.
     */
    public function up(): void
    {
        Schema::create('benefits', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->boolean('gives_priority')->default(false);
            $table->string('boost_mode', 16)->nullable(); // 'replace' | 'add' | null
            $table->decimal('boost_value', 3, 2)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = Carbon::now();

        $defaults = [
            [Application::BenefitOrphans, 'Дети-сироты', false, null],
            [Application::BenefitWithoutParentalCare, 'Дети без попечения родителей', false, null],
            [Application::BenefitDisabledChildren, 'Дети-инвалиды', false, null],
            [Application::BenefitDisabledGroupOne, 'Инвалиды I группы', false, null],
            [Application::BenefitDisabledGroupTwo, 'Инвалиды II группы', false, null],
            [Application::BenefitDisabledFromChildhood, 'Инвалиды с детства', false, null],
            [Application::BenefitMilitaryInjuryDisability, 'Инвалиды вследствие военной травмы', true, 'replace'],
            [Application::BenefitMilitaryDiseaseDisability, 'Инвалиды вследствие заболевания, полученного в период прохождения военной службы', true, 'replace'],
            [Application::BenefitCombatVeterans, 'Ветераны боевых действий', true, 'replace'],
            [Application::BenefitSvoChildren, 'Дети участников специальной военной операции', true, 'replace'],
        ];

        $rows = [];
        foreach ($defaults as $index => [$key, $label, $priority, $mode]) {
            $rows[] = [
                'key' => $key,
                'label' => $label,
                'gives_priority' => $priority,
                'boost_mode' => $mode,
                'boost_value' => $mode === 'replace' ? 6.00 : null,
                'sort_order' => ($index + 1) * 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Legacy-ключ для исторических заявлений (в формах не отображается).
        $rows[] = [
            'key' => 'svo',
            'label' => 'СВО',
            'gives_priority' => true,
            'boost_mode' => 'replace',
            'boost_value' => 6.00,
            'sort_order' => 100,
            'is_active' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('benefits')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('benefits');
    }
};
