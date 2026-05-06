@extends('layouts.auth')
@section('title', 'Вход | Приёмная комиссия БНК')
@section('content')
<div class="w-full max-w-md">
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-lg bg-white text-primary-600 shadow-sm ring-1 ring-white/50 mb-4">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
    </div>
    <h1 class="text-2xl font-bold text-white">Приёмная комиссия</h1>
    <p class="text-primary-100 mt-1">ГАПОУ Бугурусланский нефтяной колледж</p>
  </div>

  <div class="bg-white rounded-lg shadow p-6 sm:p-8 ring-1 ring-white/70">
    <div class="professionalitet-mark mb-6" aria-hidden="true">
      <span class="bg-primary-600"></span>
      <span class="bg-secondary-600"></span>
      <span class="bg-accent-orange"></span>
      <span class="bg-accent-steel"></span>
      <span class="bg-accent-yellow"></span>
      <span class="bg-danger"></span>
      <span class="bg-accent-blue"></span>
      <span class="bg-accent-magenta"></span>
      <span class="bg-gray-900"></span>
    </div>
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
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-600 focus:border-secondary-600 transition focus-ring @error('email') border-danger @enderror"
          placeholder="user@example.com" autocomplete="email">
        @error('email')
        <span class="mt-1 text-sm text-danger" role="alert">{{ $message }}</span>
        @enderror
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Пароль</label>
        <input type="password" id="password" name="password" required minlength="6"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-600 focus:border-secondary-600 transition focus-ring"
          placeholder="••••••••" autocomplete="current-password">
      </div>
      <div class="flex items-center justify-between">
        <label class="flex items-center"><input type="checkbox" name="remember" class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-600 focus-ring"><span class="ml-2 text-sm text-gray-600">Запомнить меня</span></label>
      </div>
      <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition focus:ring-4 focus:ring-secondary-100 focus-ring">Войти в систему</button>
    </form>
    <div class="mt-6 pt-6 border-t border-gray-200">
      <p class="text-center text-sm text-gray-600">Нет аккаунта? <a href="{{ route('register') }}" class="text-accent-blue hover:text-primary-700 font-medium">Зарегистрироваться</a></p>
    </div>
  </div>
  <p class="mt-6 text-center text-xs text-primary-100">© {{ date('Y') }} ГАПОУ Бугурусланский нефтяной колледж</p>
</div>
@endsection
