<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $middleName = trim((string) $this->input('middle_name'));

        $this->merge([
            'last_name' => trim((string) $this->input('last_name')),
            'first_name' => trim((string) $this->input('first_name')),
            'middle_name' => $middleName === '' ? null : $middleName,
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s-]+$/u'],
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s-]+$/u'],
            'middle_name' => ['nullable', 'string', 'max:100', 'regex:/^[\p{L}\s-]+$/u'],
            'email' => ['required', 'email:rfc', 'max:255', 'regex:/^[^\s@]+@[^\s@]+\.[A-Za-zА-Яа-яЁё]{2,}$/u', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->numbers()],
            'consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'last_name.required' => 'Фамилия обязательна.',
            'last_name.regex' => 'Фамилия может содержать только буквы, пробелы и дефис.',
            'first_name.required' => 'Имя обязательно.',
            'first_name.regex' => 'Имя может содержать только буквы, пробелы и дефис.',
            'middle_name.regex' => 'Отчество может содержать только буквы, пробелы и дефис.',
            'email.required' => 'Укажите email.',
            'email.email' => 'Укажите корректный email.',
            'email.regex' => 'Email должен содержать домен и зону, например user@example.com.',
            'email.unique' => 'Этот email уже зарегистрирован.',
            'password.required' => 'Укажите пароль.',
            'password.min' => 'Пароль минимум 8 символов.',
            'password.numbers' => 'Пароль должен содержать хотя бы одну цифру.',
            'password.confirmed' => 'Пароли не совпадают.',
            'consent.accepted' => 'Необходимо согласие на обработку данных.',
        ];
    }
}
