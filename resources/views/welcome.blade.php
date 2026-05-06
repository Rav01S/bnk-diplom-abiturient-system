<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <main class="flex min-h-screen items-center justify-center p-6">
        <div class="w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
            <h1 class="text-xl font-semibold">Приёмная комиссия</h1>
            <div class="mt-6 flex justify-center gap-3">
                @auth
                    <a href="{{ route('home') }}" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Открыть портал</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Войти</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Регистрация</a>
                    @endif
                @endauth
            </div>
        </div>
    </main>
</body>
</html>
