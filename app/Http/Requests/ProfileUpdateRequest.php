<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации профиля абитуриента.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $applicant = $this->user()?->applicant;

        return [
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:today'],
            'passport_series' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
            'passport_number' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
            'passport_issued_by' => ['required', 'string', 'max:255'],
            'snils' => ['required', 'string', 'regex:/^\d{3}-\d{3}-\d{3} \d{2}$/'],
            'phone' => ['required', 'string', 'regex:/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/'],
            'prev_education' => ['required', 'string', 'in:9class,11class,spo,vo'],
            'edu_doc_series' => ['nullable', 'string', 'max:10'],
            'edu_doc_number' => ['nullable', 'string', 'max:20'],
            'edu_doc_issued_by' => ['nullable', 'string', 'max:255'],
            'edu_issue_date' => ['nullable', 'date'],
            'avg_cert_score' => ['required', 'numeric', 'min:2', 'max:5'],
            'photo_passport' => [$applicant?->photo_passport ? 'nullable' : 'required', 'image', 'max:5120'],
            'photo_snils' => [$applicant?->photo_snils ? 'nullable' : 'required', 'image', 'max:5120'],
            'photo_edu_1' => ['nullable', 'image', 'max:5120'],
            'photo_edu_2' => ['nullable', 'image', 'max:5120'],
            'photo_edu_3' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'last_name.required' => 'Фамилия обязательна.',
            'first_name.required' => 'Имя обязательно.',
            'birth_date.required' => 'Укажите дату рождения.',
            'birth_date.before' => 'Дата рождения должна быть в прошлом.',
            'passport_series.required' => 'Укажите серию паспорта.',
            'passport_series.size' => 'Серия паспорта — 4 цифры.',
            'passport_number.required' => 'Укажите номер паспорта.',
            'passport_number.size' => 'Номер паспорта — 6 цифр.',
            'passport_issued_by.required' => 'Укажите, кем выдан паспорт.',
            'snils.required' => 'СНИЛС обязателен.',
            'snils.regex' => 'СНИЛС должен быть в формате 123-456-789 01.',
            'phone.required' => 'Укажите номер телефона.',
            'phone.regex' => 'Телефон должен быть в формате +7 (999) 123-45-67.',
            'prev_education.required' => 'Выберите уровень образования.',
            'avg_cert_score.required' => 'Укажите средний балл аттестата.',
            'avg_cert_score.min' => 'Средний балл не может быть ниже 2.',
            'avg_cert_score.max' => 'Средний балл не может быть выше 5.',
            'photo_passport.image' => 'Загрузите изображение паспорта.',
            'photo_passport.required' => 'Загрузите фото разворота паспорта.',
            'photo_passport.max' => 'Фото паспорта не более 5 МБ.',
            'photo_snils.required' => 'Загрузите фото СНИЛС.',
            'photo_snils.image' => 'Загрузите изображение СНИЛС.',
            'photo_snils.max' => 'Фото СНИЛС не более 5 МБ.',
        ];
    }
}
