<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            if (Auth::user()->is_active === false) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Учётная запись деактивирована.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return match (Auth::user()->role) {
                'applicant' => redirect()->route('applicant.dashboard'),
                'commission' => redirect()->route('commission.dashboard'),
                'admin' => redirect()->route('admin.dashboard'),
            };
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
