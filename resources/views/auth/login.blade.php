@extends('layouts.auth')
@section('title', 'Вход | Портал приёмной комиссии')
@section('content')
<div class="w-full max-w-md">
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 mb-4">
      <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Портал приёмной комиссии</h1>
    <p class="text-gray-500 mt-1">Авторизация в системе</p>
  </div>

  <div class="bg-white rounded-lg shadow p-6 sm:p-8">
    @if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
      @foreach($errors->all() as $error)
      <p>{{ $error }}</p>
      @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5" novalidate>
      @csrf
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Электронная почта</label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring @error('email') border-danger @enderror"
          placeholder="user@example.com" autocomplete="email">
        @error('email')
        <span class="mt-1 text-sm text-danger" role="alert">{{ $message }}</span>
        @enderror
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Пароль</label>
        <input type="password" id="password" name="password" required minlength="6"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"
          placeholder="••••••••" autocomplete="current-password">
      </div>
      <div class="flex items-center justify-between">
        <label class="flex items-center"><input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-600 focus-ring"><span class="ml-2 text-sm text-gray-600">Запомнить меня</span></label>
      </div>
      <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition focus:ring-4 focus:ring-primary-100 focus-ring">Войти в систему</button>
    </form>
    <div class="mt-6 pt-6 border-t border-gray-200">
      <p class="text-center text-sm text-gray-600">Нет аккаунта? <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-medium">Зарегистрироваться</a></p>
    </div>
    <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
      <p class="text-xs text-gray-500 mb-2 font-medium">🧪 Демо-доступ (пароль: password):</p>
      <div class="space-y-1 text-xs">
        <p class="px-2 py-1">👤 Абитуриент: ivanov@mail.ru</p>
        <p class="px-2 py-1">📋 Сотрудник: smirnova@portal.ru</p>
        <p class="px-2 py-1">⚙️ Администратор: admin@portal.ru</p>
      </div>
    </div>
  </div>
  <p class="mt-6 text-center text-xs text-gray-500">© 2024 Портал приёмной комиссии</p>
</div>
@endsection
