@extends('layouts.auth')
@section('title', 'Вход | Приёмная комиссия БНК')
@section('content')
<div class="w-full max-w-md">
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-16 h-16 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-white/50 mb-4">
      <img src="{{ asset('logo.gif') }}" alt="Логотип БНК" class="h-14 w-14 object-contain">
    </div>
    <h1 class="text-2xl font-bold text-white">Приёмная комиссия</h1>
    <p class="text-primary-100 mt-1">ГАПОУ Бугурусланский нефтяной колледж</p>
  </div>

  <div class="auth-card bg-white rounded-lg shadow p-6 sm:p-8 ring-1 ring-white/70">
    @if($errors->any())
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
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
          class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring @error('email') border-danger @enderror"
          placeholder="user@example.com" autocomplete="email">
        @error('email')
        <span class="mt-1 text-sm text-danger" role="alert">{{ $message }}</span>
        @enderror
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Пароль</label>
        <input type="password" id="password" name="password" required minlength="6"
          class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"
          placeholder="••••••••" autocomplete="current-password">
      </div>
      <div class="flex items-center justify-between">
        <label class="flex items-center"><input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-600 focus-ring"><span class="ml-2 text-sm text-gray-600">Запомнить меня</span></label>
      </div>
      <button type="submit" class="auth-chevron-button w-full bg-sky-500 hover:bg-sky-600 text-white font-medium py-2.5 px-4 rounded-lg transition focus:ring-4 focus:ring-sky-100 focus-ring">
        <span class="relative">Войти в систему</span>
        <span class="auth-chevron-track" aria-hidden="true">
          <span class="auth-chevron"></span>
          <span class="auth-chevron"></span>
          <span class="auth-chevron"></span>
          <span class="auth-chevron"></span>
          <span class="auth-chevron"></span>
        </span>
      </button>
    </form>
    <div class="mt-6 pt-6 border-t border-gray-200">
      <p class="text-center text-sm text-gray-600">Нет аккаунта? <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-medium">Зарегистрироваться</a></p>
    </div>
  </div>
  <p class="mt-6 text-center text-xs text-primary-100">© {{ date('Y') }} ГАПОУ Бугурусланский нефтяной колледж</p>
</div>
@endsection
