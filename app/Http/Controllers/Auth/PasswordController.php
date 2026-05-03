<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Показ формы смены пароля.
     */
    public function edit(): View
    {
        return view('auth.password-edit');
    }

    /**
     * Обновление пароля.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Укажите текущий пароль.',
            'current_password.current_password' => 'Текущий пароль указан неверно.',
            'password.required' => 'Укажите новый пароль.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min' => 'Новый пароль должен быть не менее 8 символов.',
        ]);

        $user = $request->user();
        $user->update([
            'password_hash' => Hash::make($validated['password']),
        ]);

        AuditLog::record($request, 'user.password_self_reset', $user->email);

        return back()->with('success', 'Пароль успешно изменён!');
    }
}
