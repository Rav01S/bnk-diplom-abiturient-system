<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации решения комиссии.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $rules = [
            'decision' => ['required', 'in:approved,rejected,rework_needed'],
            'avg_cert_score' => ['nullable', 'numeric', 'min:2', 'max:5'],
        ];

        if ($this->input('decision') === 'approved') {
            $rules['avg_cert_score'] = ['required', 'numeric', 'min:2', 'max:5'];
        }

        // Причина обязательна при отклонении или отправке на доработку
        if (in_array($this->input('decision'), ['rejected', 'rework_needed'])) {
            $rules['rejection_reason'] = ['required', 'string', 'min:10', 'max:1000'];
        } else {
            $rules['rejection_reason'] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'decision.required' => 'Выберите решение.',
            'decision.in' => 'Недопустимый тип решения.',
            'rejection_reason.required' => 'Укажите комментарий сотрудника (минимум 10 символов).',
            'rejection_reason.min' => 'Комментарий сотрудника должен содержать минимум 10 символов.',
        ];
    }
}
