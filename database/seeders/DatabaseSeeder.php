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
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Заполнение БД тестовыми данными: 15 в главных, 30 в подчиненных.
     */
    public function run(): void
    {
        $faker = Faker::create('ru_RU');

        // Создаем директорию для документов
        Storage::disk('public')->makeDirectory('documents');

        $templateImage = base_path('templates/заглушка.png');
        $hasTemplate = file_exists($templateImage);
        
        $photosCreated = 0;

        $this->command->info('Начинаем заполнение базы данных...');

        // === Администраторы и Комиссия ===
        User::create([
            'email' => 'admin@portal.ru',
            'password_hash' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'email' => 'smirnova@portal.ru',
            'password_hash' => Hash::make('password'),
            'role' => 'commission',
            'is_active' => true,
        ]);

        // === Главные таблицы: 15 записей (Специальности) ===
        $this->command->info('Создание 15 специальностей (главная таблица)...');
        $specialties = [];
        for ($i = 1; $i <= 15; $i++) {
            $specialties[] = Specialty::create([
                'code' => $faker->unique()->numerify('##.02.0#'),
                'name' => 'Специальность ' . $faker->word() . ' ' . $i,
                'subject_1' => 'Математика',
                'subject_2' => 'Русский язык',
                'subject_3' => $faker->randomElement(['Информатика', 'Обществознание', 'Физика', 'История']),
            ]);
        }

        // === Подчиненные таблицы: 30 записей (Программы) ===
        $this->command->info('Создание 30 программ (подчиненная таблица)...');
        $programs = [];
        $currentYear = (int) date('Y');
        
        foreach ($specialties as $specialty) {
            foreach ([0, 1] as $yearOffset) {
                $programs[] = Program::create([
                    'specialty_id' => $specialty->id,
                    'campaign_year' => $currentYear - $yearOffset,
                    'has_study_form' => $faker->boolean,
                    'plan_count' => $faker->numberBetween(20, 50),
                    'plan_count_paid' => $faker->numberBetween(10, 30),
                    'is_open' => true,
                    'open_from' => '2024-06-01',
                    'open_until' => '2026-08-15',
                ]);
            }
        }

        // === Абитуриенты (30 пользователей и 30 профилей) ===
        $this->command->info('Создание 30 профилей абитуриентов и фото...');
        $applicants = [];
        for ($i = 1; $i <= 30; $i++) {
            $email = $i === 1 ? 'ivanov@mail.ru' : "applicant{$i}@mail.ru";
            $lastName = $i === 1 ? 'Иванов' : $faker->lastName;
            $firstName = $i === 1 ? 'Иван' : $faker->firstName;
            $middleName = $i === 1 ? 'Иванович' : $faker->middleName;

            $user = User::create([
                'email' => $email,
                'password_hash' => Hash::make('password'),
                'role' => 'applicant',
                'is_active' => true,
            ]);

            // Фотографии абитуриента
            $photos = [];
            foreach (['passport', 'snils', 'edu_1', 'edu_2', 'edu_3'] as $type) {
                $filename = "documents/applicant_{$user->id}_{$type}.png";
                if ($hasTemplate) {
                    Storage::disk('public')->put($filename, file_get_contents($templateImage));
                    $photosCreated++;
                }
                $photos["photo_{$type}"] = $filename;
            }

            $applicant = Applicant::create(array_merge([
                'user_id' => $user->id,
                'last_name' => $lastName,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'birth_date' => $faker->date('Y-m-d', '2008-01-01'),
                'passport_series' => (string) $faker->numberBetween(1000, 9999),
                'passport_number' => (string) $faker->numberBetween(100000, 999999),
                'passport_issued_by' => 'ОУФМС ' . mb_substr($faker->city, 0, 50),
                'snils' => $faker->numerify('###-###-### ##'),
                'phone' => '+7 ' . $faker->numerify('(###) ###-##-##'),
                'prev_education' => $faker->randomElement(['9class', '11class']),
                'edu_doc_series' => strtoupper(Str::random(2)),
                'edu_doc_number' => (string) $faker->numberBetween(1000000, 9999999),
                'edu_doc_issued_by' => mb_substr('Школа ' . $faker->company, 0, 50),
                'edu_issue_date' => $faker->date('Y-m-d', '2024-06-30'),
                'avg_cert_score' => $faker->randomFloat(2, 3, 5),
            ], $photos));

            $applicants[] = $applicant;
        }

        // === Заявления (30 записей) ===
        $this->command->info('Создание 30 заявлений и снапшотов фото...');
        foreach ($applicants as $index => $applicant) {
            $program = $programs[$index % 30]; 

            // Фотографии заявления
            $appPhotos = [];
            foreach (['passport', 'snils', 'edu_1', 'edu_2', 'edu_3'] as $type) {
                $filename = "documents/app_{$applicant->id}_{$program->id}_{$type}.png";
                if ($hasTemplate) {
                    Storage::disk('public')->put($filename, file_get_contents($templateImage));
                    $photosCreated++;
                }
                $appPhotos["app_photo_{$type}"] = $filename;
            }
            
            $signedDocFilename = "documents/app_{$applicant->id}_{$program->id}_signed_doc.png";
            if ($hasTemplate) {
                Storage::disk('public')->put($signedDocFilename, file_get_contents($templateImage));
                $photosCreated++;
            }

            $application = Application::create(array_merge([
                'applicant_id' => $applicant->id,
                'program_id' => $program->id,
                'priority' => 1,
                'status' => $faker->randomElement(['draft', 'submitted', 'approved', 'rejected', 'rework_needed', 'cancelled']),
                'revision' => 1,
                'doc_type' => $faker->randomElement(['original', 'copy']),
                'study_form' => $faker->randomElement(['full_time', 'part_time']),
                'funding_type' => $faker->randomElement(['budget', 'paid']),
                'is_benefit' => $faker->boolean(20),
                'needs_dorm' => $faker->boolean,
                'is_first_spo' => true,
                'app_last_name' => $applicant->last_name,
                'app_first_name' => $applicant->first_name,
                'app_middle_name' => $applicant->middle_name,
                'app_birth_date' => $applicant->birth_date,
                'app_passport_series' => $applicant->passport_series,
                'app_passport_number' => $applicant->passport_number,
                'app_passport_issued_by' => $applicant->passport_issued_by,
                'app_snils' => $applicant->snils,
                'app_prev_education' => $applicant->prev_education,
                'app_edu_doc_series' => $applicant->edu_doc_series,
                'app_edu_doc_number' => $applicant->edu_doc_number,
                'app_edu_doc_issued_by' => $applicant->edu_doc_issued_by,
                'app_edu_issue_date' => $applicant->edu_issue_date,
                'app_avg_cert_score' => $applicant->avg_cert_score,
                'app_phone' => $applicant->phone,
                'signed_doc_photo' => $signedDocFilename,
            ], $appPhotos));

            // Оценки по предметам для заявления
            $specialty = $program->specialty;
            $subjects = array_filter([$specialty->subject_1, $specialty->subject_2, $specialty->subject_3]);
            
            foreach ($subjects as $subject) {
                ApplicationScore::create([
                    'application_id' => $application->id,
                    'subject_name' => $subject,
                    'score' => $faker->randomFloat(1, 3, 5),
                ]);
            }
        }

        $this->command->info("Готово! Создано файлов фото: {$photosCreated}.");
    }
}
