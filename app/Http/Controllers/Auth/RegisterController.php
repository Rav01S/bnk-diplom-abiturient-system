<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Регистрация абитуриента — создаёт User + Applicant в транзакции.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'consent' => ['accepted'],
        ], [
            'last_name.required' => 'Фамилия обязательна.',
            'first_name.required' => 'Имя обязательно.',
            'email.required' => 'Укажите email.',
            'email.unique' => 'Этот email уже зарегистрирован.',
            'password.required' => 'Укажите пароль.',
            'password.min' => 'Пароль минимум 8 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
            'consent.accepted' => 'Необходимо согласие на обработку данных.',
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'role' => 'applicant',
            ]);

            Applicant::create([
                'user_id' => $user->id,
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('applicant.dashboard');
    }
}
