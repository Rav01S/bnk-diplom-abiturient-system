<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Program;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Заполнение БД реалистичными тестовыми данными.
     * 1 админ, 2 сотрудника, 3 абитуриента, 2 специальности, 3 программы, 5 заявлений.
     */
    public function run(): void
    {
        // === Пользователи ===
        $admin = User::create([
            'email' => 'admin@portal.ru',
            'password_hash' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $commission1 = User::create([
            'email' => 'smirnova@portal.ru',
            'password_hash' => Hash::make('password'),
            'role' => 'commission',
        ]);

        $commission2 = User::create([
            'email' => 'kozlov@portal.ru',
            'password_hash' => Hash::make('password'),
            'role' => 'commission',
        ]);

        $user1 = User::create([
            'email' => 'ivanov@mail.ru',
            'password_hash' => Hash::make('password'),
            'role' => 'applicant',
        ]);

        $user2 = User::create([
            'email' => 'petrova@mail.ru',
            'password_hash' => Hash::make('password'),
            'role' => 'applicant',
        ]);

        $user3 = User::create([
            'email' => 'sidorov@mail.ru',
            'password_hash' => Hash::make('password'),
            'role' => 'applicant',
        ]);

        // === Профили абитуриентов ===
        $applicant1 = Applicant::create([
            'user_id' => $user1->id,
            'last_name' => 'Иванов',
            'first_name' => 'Иван',
            'middle_name' => 'Иванович',
            'birth_date' => '2006-03-15',
            'passport_series' => '4510',
            'passport_number' => '123456',
            'passport_issued_by' => 'ОУФМС России по г. Москве, отдел №1',
            'snils' => '123-456-789 01',
            'phone' => '+7 (999) 123-45-67',
            'prev_education' => '11class',
            'edu_doc_series' => 'АА',
            'edu_doc_number' => '1234567',
            'edu_doc_issued_by' => 'ГБОУ СОШ №100',
            'edu_issue_date' => '2024-06-20',
            'avg_cert_score' => 4.75,
        ]);

        $applicant2 = Applicant::create([
            'user_id' => $user2->id,
            'last_name' => 'Петрова',
            'first_name' => 'Анна',
            'middle_name' => 'Сергеевна',
            'birth_date' => '2006-07-22',
            'passport_series' => '4511',
            'passport_number' => '654321',
            'passport_issued_by' => 'ОУФМС России по г. Санкт-Петербургу',
            'snils' => '987-654-321 09',
            'phone' => '+7 (912) 987-65-43',
            'prev_education' => '9class',
            'edu_doc_series' => 'ББ',
            'edu_doc_number' => '7654321',
            'edu_doc_issued_by' => 'ГБОУ Лицей №42',
            'edu_issue_date' => '2024-06-18',
            'avg_cert_score' => 4.50,
        ]);

        $applicant3 = Applicant::create([
            'user_id' => $user3->id,
            'last_name' => 'Сидоров',
            'first_name' => 'Дмитрий',
            'middle_name' => 'Константинович',
            'birth_date' => '2005-11-08',
            'passport_series' => '4512',
            'passport_number' => '111222',
            'passport_issued_by' => 'ОВД по Центральному р-ну г. Казани',
            'snils' => '111-222-333 44',
            'phone' => '+7 (905) 111-22-33',
            'prev_education' => '11class',
            'edu_doc_series' => 'ВВ',
            'edu_doc_number' => '9876543',
            'edu_doc_issued_by' => 'МАОУ Гимназия №5',
            'edu_issue_date' => '2024-06-15',
            'avg_cert_score' => 4.90,
        ]);

        // === Специальности ===
        $spec1 = Specialty::create([
            'code' => '09.02.07',
            'name' => 'Информационные системы и программирование',
            'subject_1' => 'Математика',
            'subject_2' => 'Русский язык',
            'subject_3' => 'Информатика',
        ]);

        $spec2 = Specialty::create([
            'code' => '38.02.01',
            'name' => 'Экономика и бухгалтерский учёт',
            'subject_1' => 'Математика',
            'subject_2' => 'Русский язык',
            'subject_3' => 'Обществознание',
        ]);

        // === Программы ===
        $prog1 = Program::create([
            'specialty_id' => $spec1->id,
            'campaign_year' => (int) date('Y'),
            'has_study_form' => true,
            'plan_count' => 50,
            'plan_count_paid' => 30,
            'is_open' => true,
            'open_from' => '2024-06-01',
            'open_until' => '2026-08-15',
        ]);

        $prog2 = Program::create([
            'specialty_id' => $spec1->id,
            'campaign_year' => (int) date('Y'),
            'has_study_form' => false,
            'plan_count' => 25,
            'plan_count_paid' => 15,
            'is_open' => true,
            'open_from' => '2024-06-01',
            'open_until' => '2026-08-15',
        ]);

        $prog3 = Program::create([
            'specialty_id' => $spec2->id,
            'campaign_year' => (int) date('Y'),
            'has_study_form' => true,
            'plan_count' => 40,
            'plan_count_paid' => 20,
            'is_open' => true,
            'open_from' => '2024-06-01',
            'open_until' => '2026-08-15',
        ]);

        // === Заявления с разными статусами ===

        // Заявление 1: Иванов → ИСиП, submitted
        $app1 = Application::create([
            'applicant_id' => $applicant1->id,
            'program_id' => $prog1->id,
            'priority' => 1,
            'status' => 'submitted',
            'revision' => 1,
            'doc_type' => 'original',
            'study_form' => 'full_time',
            'funding_type' => 'budget',
            'is_benefit' => false,
            'needs_dorm' => true,
            'is_first_spo' => true,
            'app_last_name' => 'Иванов',
            'app_first_name' => 'Иван',
            'app_middle_name' => 'Иванович',
            'app_birth_date' => '2006-03-15',
            'app_passport_series' => '4510',
            'app_passport_number' => '123456',
            'app_passport_issued_by' => 'ОУФМС России по г. Москве, отдел №1',
            'app_snils' => '123-456-789 01',
            'app_prev_education' => '11class',
            'app_edu_doc_series' => 'АА',
            'app_edu_doc_number' => '1234567',
            'app_edu_doc_issued_by' => 'ГБОУ СОШ №100',
            'app_edu_issue_date' => '2024-06-20',
            'app_avg_cert_score' => 4.75,
            'app_phone' => '+7 (999) 123-45-67',
        ]);
        ApplicationScore::create(['application_id' => $app1->id, 'subject_name' => 'Математика', 'score' => 4.5]);
        ApplicationScore::create(['application_id' => $app1->id, 'subject_name' => 'Русский язык', 'score' => 5.0]);
        ApplicationScore::create(['application_id' => $app1->id, 'subject_name' => 'Информатика', 'score' => 4.0]);

        // Заявление 2: Иванов → Экономика, approved
        $app2 = Application::create([
            'applicant_id' => $applicant1->id,
            'program_id' => $prog3->id,
            'priority' => 2,
            'status' => 'approved',
            'revision' => 1,
            'doc_type' => 'copy',
            'study_form' => 'full_time',
            'funding_type' => 'budget',
            'is_benefit' => false,
            'needs_dorm' => false,
            'is_first_spo' => true,
            'app_last_name' => 'Иванов',
            'app_first_name' => 'Иван',
            'app_middle_name' => 'Иванович',
            'app_birth_date' => '2006-03-15',
            'app_passport_series' => '4510',
            'app_passport_number' => '123456',
            'app_passport_issued_by' => 'ОУФМС России по г. Москве, отдел №1',
            'app_snils' => '123-456-789 01',
            'app_prev_education' => '11class',
            'app_edu_doc_series' => 'АА',
            'app_edu_doc_number' => '1234567',
            'app_edu_doc_issued_by' => 'ГБОУ СОШ №100',
            'app_edu_issue_date' => '2024-06-20',
            'app_avg_cert_score' => 4.75,
            'app_phone' => '+7 (999) 123-45-67',
        ]);
        ApplicationScore::create(['application_id' => $app2->id, 'subject_name' => 'Математика', 'score' => 4.5]);
        ApplicationScore::create(['application_id' => $app2->id, 'subject_name' => 'Русский язык', 'score' => 5.0]);
        ApplicationScore::create(['application_id' => $app2->id, 'subject_name' => 'Обществознание', 'score' => 4.0]);

        // Заявление 3: Петрова → ИСиП, rejected (ревизия 2)
        $app3 = Application::create([
            'applicant_id' => $applicant2->id,
            'program_id' => $prog1->id,
            'priority' => 1,
            'status' => 'rejected',
            'revision' => 2,
            'rejection_reason' => 'Неверно заполнены паспортные данные. Проверьте серию и номер паспорта.',
            'doc_type' => 'original',
            'study_form' => 'part_time',
            'funding_type' => 'paid',
            'is_benefit' => false,
            'needs_dorm' => false,
            'is_first_spo' => true,
            'app_last_name' => 'Петрова',
            'app_first_name' => 'Анна',
            'app_middle_name' => 'Сергеевна',
            'app_birth_date' => '2006-07-22',
            'app_passport_series' => '4511',
            'app_passport_number' => '654321',
            'app_passport_issued_by' => 'ОУФМС России по г. Санкт-Петербургу',
            'app_snils' => '987-654-321 09',
            'app_prev_education' => '9class',
            'app_edu_doc_series' => 'ББ',
            'app_edu_doc_number' => '7654321',
            'app_edu_doc_issued_by' => 'ГБОУ Лицей №42',
            'app_edu_issue_date' => '2024-06-18',
            'app_avg_cert_score' => 4.50,
            'app_phone' => '+7 (912) 987-65-43',
        ]);
        ApplicationScore::create(['application_id' => $app3->id, 'subject_name' => 'Математика', 'score' => 3.5]);
        ApplicationScore::create(['application_id' => $app3->id, 'subject_name' => 'Русский язык', 'score' => 4.0]);
        ApplicationScore::create(['application_id' => $app3->id, 'subject_name' => 'Информатика', 'score' => 3.0]);

        // Заявление 4: Сидоров → ИСиП (заочная), rework_needed
        $app4 = Application::create([
            'applicant_id' => $applicant3->id,
            'program_id' => $prog2->id,
            'priority' => 1,
            'status' => 'rework_needed',
            'revision' => 1,
            'rejection_reason' => 'Фото аттестата нечитаемо. Загрузите более качественный скан.',
            'doc_type' => 'original',
            'study_form' => 'full_time',
            'funding_type' => 'budget',
            'is_benefit' => true,
            'benefit_type' => 'olympiad',
            'needs_dorm' => true,
            'is_first_spo' => true,
            'app_last_name' => 'Сидоров',
            'app_first_name' => 'Дмитрий',
            'app_middle_name' => 'Константинович',
            'app_birth_date' => '2005-11-08',
            'app_passport_series' => '4512',
            'app_passport_number' => '111222',
            'app_passport_issued_by' => 'ОВД по Центральному р-ну г. Казани',
            'app_snils' => '111-222-333 44',
            'app_prev_education' => '11class',
            'app_edu_doc_series' => 'ВВ',
            'app_edu_doc_number' => '9876543',
            'app_edu_doc_issued_by' => 'МАОУ Гимназия №5',
            'app_edu_issue_date' => '2024-06-15',
            'app_avg_cert_score' => 4.90,
            'app_phone' => '+7 (905) 111-22-33',
        ]);
        ApplicationScore::create(['application_id' => $app4->id, 'subject_name' => 'Математика', 'score' => 5.0]);
        ApplicationScore::create(['application_id' => $app4->id, 'subject_name' => 'Русский язык', 'score' => 4.5]);
        ApplicationScore::create(['application_id' => $app4->id, 'subject_name' => 'Информатика', 'score' => 5.0]);

        // Заявление 5: Петрова → Экономика, cancelled
        $app5 = Application::create([
            'applicant_id' => $applicant2->id,
            'program_id' => $prog3->id,
            'priority' => 3,
            'status' => 'cancelled',
            'revision' => 1,
            'doc_type' => 'copy',
            'study_form' => 'full_time',
            'funding_type' => 'paid',
            'is_benefit' => false,
            'needs_dorm' => false,
            'is_first_spo' => true,
            'cancelled_at' => now(),
            'app_last_name' => 'Петрова',
            'app_first_name' => 'Анна',
            'app_middle_name' => 'Сергеевна',
            'app_birth_date' => '2006-07-22',
            'app_passport_series' => '4511',
            'app_passport_number' => '654321',
            'app_passport_issued_by' => 'ОУФМС России по г. Санкт-Петербургу',
            'app_snils' => '987-654-321 09',
            'app_prev_education' => '9class',
            'app_edu_doc_series' => 'ББ',
            'app_edu_doc_number' => '7654321',
            'app_edu_doc_issued_by' => 'ГБОУ Лицей №42',
            'app_edu_issue_date' => '2024-06-18',
            'app_avg_cert_score' => 4.50,
            'app_phone' => '+7 (912) 987-65-43',
        ]);
        ApplicationScore::create(['application_id' => $app5->id, 'subject_name' => 'Математика', 'score' => 3.0]);
        ApplicationScore::create(['application_id' => $app5->id, 'subject_name' => 'Русский язык', 'score' => 4.5]);
        ApplicationScore::create(['application_id' => $app5->id, 'subject_name' => 'Обществознание', 'score' => 3.5]);
    }
}
