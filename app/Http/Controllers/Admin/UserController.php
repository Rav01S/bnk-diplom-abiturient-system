<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::whereIn('role', ['commission', 'admin'])
            ->orderBy('id')
            ->get();

        return view('admin.users', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['commission', 'admin'])],
        ], [
            'email.required' => 'Укажите email.',
            'email.unique' => 'Этот email уже занят.',
            'password.required' => 'Укажите пароль.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'role.required' => 'Выберите роль.',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        AuditLog::record($request, 'staff.created', $user->email, [
            'role' => $user->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'Сотрудник добавлен.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureStaffAccount($user);

        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['commission', 'admin'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($user->id === $request->user()?->id && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Нельзя снять роль администратора со своей учётной записи.']);
        }

        $user->fill([
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        AuditLog::record($request, 'staff.updated', $user->email, [
            'staff_id' => $user->id,
            'role' => $user->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'Данные сотрудника обновлены.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $this->ensureStaffAccount($user);

        $user->update([
            'password_hash' => Hash::make('password123'),
        ]);

        AuditLog::record($request, 'staff.password_reset', $user->email, [
            'staff_id' => $user->id,
        ]);

        return redirect()->route('admin.users')->with('success', 'Пароль сброшен на password123.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        $this->ensureStaffAccount($user);

        if ($user->id === $request->user()?->id) {
            return back()->withErrors(['user' => 'Нельзя деактивировать свою учётную запись.']);
        }

        $user->update([
            'is_active' => ! ($user->is_active ?? true),
        ]);

        AuditLog::record($request, ($user->is_active ?? true) ? 'staff.activated' : 'staff.deactivated', $user->email, [
            'staff_id' => $user->id,
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', ($user->is_active ?? true) ? 'Сотрудник активирован.' : 'Сотрудник деактивирован.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureStaffAccount($user);

        if ($user->id === $request->user()?->id) {
            return back()->withErrors(['delete' => 'Невозможно удалить собственный аккаунт.']);
        }

        $email = $user->email;
        $user->delete();

        AuditLog::record($request, 'staff.deleted', $email);

        return redirect()->route('admin.users')->with('success', 'Пользователь удалён.');
    }

    private function ensureStaffAccount(User $user): void
    {
        abort_unless(in_array($user->role, ['commission', 'admin'], true), 404);
    }
}
