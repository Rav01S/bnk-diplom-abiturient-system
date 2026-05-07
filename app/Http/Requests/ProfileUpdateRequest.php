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
            'avg_cert_score' => ['nullable', 'numeric', 'min:2', 'max:5'],
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
            'last_name.string' => 'Фамилия должна быть строкой.',
            'last_name.max' => 'Фамилия не должна быть длиннее 100 символов.',
            'last_name.required' => 'Фамилия обязательна.',
            'first_name.string' => 'Имя должно быть строкой.',
            'first_name.max' => 'Имя не должно быть длиннее 100 символов.',
            'first_name.required' => 'Имя обязательно.',
            'middle_name.string' => 'Отчество должно быть строкой.',
            'middle_name.max' => 'Отчество не должно быть длиннее 100 символов.',
            'birth_date.required' => 'Укажите дату рождения.',
            'birth_date.date' => 'Укажите корректную дату рождения.',
            'birth_date.before' => 'Дата рождения должна быть в прошлом.',
            'passport_series.required' => 'Укажите серию паспорта.',
            'passport_series.size' => 'Серия паспорта — 4 цифры.',
            'passport_series.regex' => 'Серия паспорта должна состоять из 4 цифр.',
            'passport_series.string' => 'Серия паспорта должна быть строкой.',
            'passport_number.required' => 'Укажите номер паспорта.',
            'passport_number.size' => 'Номер паспорта — 6 цифр.',
            'passport_number.regex' => 'Номер паспорта должен состоять из 6 цифр.',
            'passport_number.string' => 'Номер паспорта должен быть строкой.',
            'passport_issued_by.required' => 'Укажите, кем выдан паспорт.',
            'passport_issued_by.string' => 'Поле «Кем выдан» должно быть строкой.',
            'passport_issued_by.max' => 'Поле «Кем выдан» не должно быть длиннее 255 символов.',
            'snils.required' => 'СНИЛС обязателен.',
            'snils.string' => 'СНИЛС должен быть строкой.',
            'snils.regex' => 'СНИЛС должен быть в формате 123-456-789 01.',
            'phone.required' => 'Укажите номер телефона.',
            'phone.string' => 'Телефон должен быть строкой.',
            'phone.regex' => 'Телефон должен быть в формате +7 (999) 123-45-67.',
            'prev_education.required' => 'Выберите уровень образования.',
            'prev_education.in' => 'Выберите корректный уровень образования.',
            'prev_education.string' => 'Уровень образования должен быть строкой.',
            'edu_doc_series.string' => 'Серия документа об образовании должна быть строкой.',
            'edu_doc_series.max' => 'Серия документа об образовании не должна быть длиннее 10 символов.',
            'edu_doc_number.string' => 'Номер документа об образовании должен быть строкой.',
            'edu_doc_number.max' => 'Номер документа об образовании не должен быть длиннее 20 символов.',
            'edu_doc_issued_by.string' => 'Поле «Кем выдан документ об образовании» должно быть строкой.',
            'edu_doc_issued_by.max' => 'Поле «Кем выдан документ об образовании» не должно быть длиннее 255 символов.',
            'edu_issue_date.date' => 'Укажите корректную дату выдачи документа об образовании.',
            'avg_cert_score.numeric' => 'Средний балл аттестата должен быть числом.',
            'avg_cert_score.min' => 'Средний балл не может быть ниже 2.',
            'avg_cert_score.max' => 'Средний балл не может быть выше 5.',
            'photo_passport.image' => 'Загрузите изображение паспорта.',
            'photo_passport.required' => 'Загрузите фото разворота паспорта.',
            'photo_passport.max' => 'Фото паспорта не более 5 МБ.',
            'photo_passport.uploaded' => 'Не удалось загрузить фото паспорта. Проверьте размер и формат файла.',
            'photo_snils.required' => 'Загрузите фото СНИЛС.',
            'photo_snils.image' => 'Загрузите изображение СНИЛС.',
            'photo_snils.max' => 'Фото СНИЛС не более 5 МБ.',
            'photo_snils.uploaded' => 'Не удалось загрузить фото СНИЛС. Проверьте размер и формат файла.',
            'photo_edu_1.image' => 'Фото документа об образовании должно быть изображением.',
            'photo_edu_1.max' => 'Фото документа об образовании не более 5 МБ.',
            'photo_edu_1.uploaded' => 'Не удалось загрузить фото документа об образовании. Проверьте размер и формат файла.',
            'photo_edu_2.image' => 'Фото документа об образовании должно быть изображением.',
            'photo_edu_2.max' => 'Фото документа об образовании не более 5 МБ.',
            'photo_edu_2.uploaded' => 'Не удалось загрузить фото документа об образовании. Проверьте размер и формат файла.',
            'photo_edu_3.image' => 'Фото приложения к документу об образовании должно быть изображением.',
            'photo_edu_3.max' => 'Фото приложения к документу об образовании не более 5 МБ.',
            'photo_edu_3.uploaded' => 'Не удалось загрузить фото приложения к документу об образовании. Проверьте размер и формат файла.',
        ];
    }
}
