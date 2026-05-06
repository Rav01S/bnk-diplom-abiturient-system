@extends('layouts.auth')
@section('title', 'Регистрация | Приёмная комиссия БНК')
@section('content')
<div class="w-full max-w-lg">
  <div class="text-center mb-6">
    <a href="{{ route('login') }}" class="inline-flex items-center text-secondary-100 hover:text-white mb-4">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Назад ко входу
    </a>
    <h1 class="text-2xl font-bold text-white">Регистрация абитуриента</h1>
    <p class="text-primary-100 mt-1">Приёмная комиссия ГАПОУ Бугурусланский нефтяной колледж</p>
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
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div><label for="last_name" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Фамилия</label><input type="text" id="last_name" name="last_name" required maxlength="100" pattern="[А-Яа-яЁёA-Za-z\s-]+" value="{{ old('last_name') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-600 focus:border-secondary-600 transition focus-ring" placeholder="Иванов"></div>
        <div><label for="first_name" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Имя</label><input type="text" id="first_name" name="first_name" required maxlength="100" pattern="[А-Яа-яЁёA-Za-z\s-]+" value="{{ old('first_name') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-600 focus:border-secondary-600 transition focus-ring" placeholder="Иван"></div>
        <div><label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Отчество</label><input type="text" id="middle_name" name="middle_name" maxlength="100" pattern="[А-Яа-яЁёA-Za-z\s-]+" value="{{ old('middle_name') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-600 focus:border-secondary-600 transition focus-ring" placeholder="Иванович"></div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label for="email" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Email</label><input type="email" id="email" name="email" required maxlength="255" value="{{ old('email') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-600 focus:border-secondary-600 transition focus-ring" placeholder="user@example.com" autocomplete="email"></div>
        <div><label for="password" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Пароль</label><input type="password" id="password" name="password" required minlength="8" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-600 focus:border-secondary-600 transition focus-ring" placeholder="••••••••" autocomplete="new-password"><span class="mt-1 text-xs text-gray-500">Мин. 8 символов</span></div>
      </div>
      <div><label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Подтвердите пароль</label><input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-600 focus:border-secondary-600 transition focus-ring" placeholder="••••••••" autocomplete="new-password"></div>
      <div class="flex items-start"><input type="checkbox" id="consent" name="consent" required class="mt-1 w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-600 focus-ring"><label for="consent" class="ml-2 text-sm text-gray-600">Я даю согласие на обработку персональных данных в соответствии с <a href="https://kapmed.ru/upload/iblock/739/bm38k7g738luplja952kid7bu4n0mdpb.pdf" target="_blank" rel="noopener noreferrer" class="text-accent-blue hover:underline">152-ФЗ</a></label></div>
      <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition focus:ring-4 focus:ring-secondary-100 focus-ring">Зарегистрироваться</button>
    </form>
    <p class="mt-6 text-center text-sm text-gray-600">Уже есть аккаунт? <a href="{{ route('login') }}" class="text-accent-blue hover:text-primary-700 font-medium">Войти</a></p>
  </div>
  <p class="mt-6 text-center text-xs text-primary-100">© {{ date('Y') }} ГАПОУ Бугурусланский нефтяной колледж</p>
</div>
@endsection
