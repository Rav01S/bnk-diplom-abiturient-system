<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ApplicationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации при создании/обновлении заявления.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'priority' => ['required', 'integer', 'min:1', 'max:5'],
            'study_form' => ['required', 'in:full_time,part_time'],
            'funding_type' => ['required', 'in:budget,paid'],
            'doc_type' => ['required', 'in:original,copy'],
            'is_benefit' => ['nullable', 'boolean'],
            'benefit_type' => [$this->boolean('is_benefit') ? 'required' : 'nullable', 'string', 'max:50'],
            'needs_dorm' => ['nullable', 'boolean'],
            'is_first_spo' => ['nullable', 'boolean'],
            'signed_doc_photo' => [$this->isMethod('post') ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'scores' => ['required', 'array', 'min:1', 'max:5'],
            'scores.*.subject_name' => ['required', 'string', 'max:100'],
            'scores.*.score' => ['required', 'numeric', 'min:2', 'max:5'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'program_id.required' => 'Выберите программу.',
            'program_id.exists' => 'Выбранная программа не найдена.',
            'priority.required' => 'Укажите приоритет заявления.',
            'priority.min' => 'Приоритет от 1 до 5.',
            'priority.max' => 'Приоритет от 1 до 5.',
            'study_form.required' => 'Выберите форму обучения.',
            'funding_type.required' => 'Выберите тип финансирования.',
            'doc_type.required' => 'Укажите тип документа.',
            'benefit_type.required' => 'Выберите тип льготы.',
            'scores.required' => 'Укажите оценки по предметам.',
            'scores.size' => 'Необходимо указать оценки по 3 предметам.',
            'scores.*.score.required' => 'Укажите оценку.',
            'scores.*.score.min' => 'Оценка не может быть ниже 2.',
            'scores.*.score.max' => 'Оценка не может быть выше 5.',
            'signed_doc_photo.required' => 'Загрузите фото или скан подписанного заявления.',
            'signed_doc_photo.mimes' => 'Файл подписанного заявления должен быть в формате JPG, PNG или PDF.',
            'signed_doc_photo.max' => 'Файл подписанного заявления не должен превышать 10 МБ.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $applicant = $this->user()?->applicant;

            if ($applicant && ! $applicant->isProfileComplete()) {
                $validator->errors()->add('profile', 'Заполните профиль абитуриента перед подачей заявления.');
            }

            if ($applicant && $this->isMethod('post') && $this->filled('priority')) {
                $maxPriority = min($applicant->applications()->active()->count() + 1, 5);

                if ((int) $this->input('priority') > $maxPriority) {
                    $validator->errors()->add('priority', "Для нового заявления можно выбрать приоритет не выше {$maxPriority}.");
                }
            }

            $program = \App\Models\Program::find($this->input('program_id'));
            if (! $program) {
                return;
            }

            if ($this->input('study_form') === 'part_time' && ! $program->has_study_form) {
                $validator->errors()->add('study_form', 'Для выбранной программы доступна только очная форма обучения.');
            }

            if ($this->input('funding_type') === 'budget' && $program->plan_count <= 0) {
                $validator->errors()->add('funding_type', 'Для выбранной программы нет бюджетных мест.');
            }

            if ($this->input('funding_type') === 'paid' && $program->plan_count_paid <= 0) {
                $validator->errors()->add('funding_type', 'Для выбранной программы нет платных мест.');
            }
        });
    }
}
