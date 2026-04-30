@extends('layouts.auth')
@section('title', 'Регистрация | Портал приёмной комиссии')
@section('content')
<div class="w-full max-w-lg">
  <div class="text-center mb-6">
    <a href="{{ route('login') }}" class="inline-flex items-center text-gray-500 hover:text-gray-900 mb-4">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Назад ко входу
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Регистрация абитуриента</h1>
    <p class="text-gray-500 mt-1">Создайте аккаунт для подачи заявлений</p>
  </div>
  <div class="bg-white rounded-lg shadow p-6 sm:p-8">
    @if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('register.submit') }}" class="space-y-5" novalidate>
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div><label for="last_name" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Фамилия</label><input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="Иванов"></div>
        <div><label for="first_name" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Имя</label><input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="Иван"></div>
        <div><label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Отчество</label><input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="Иванович"></div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label for="email" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Email</label><input type="email" id="email" name="email" required value="{{ old('email') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="user@example.com"></div>
        <div><label for="password" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Пароль</label><input type="password" id="password" name="password" required minlength="8" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="••••••••"><span class="mt-1 text-xs text-gray-500">Мин. 8 символов</span></div>
      </div>
      <div><label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Подтвердите пароль</label><input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="••••••••"></div>
      <div class="flex items-start"><input type="checkbox" id="consent" name="consent" required class="mt-1 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-600 focus-ring"><label for="consent" class="ml-2 text-sm text-gray-600">Я даю согласие на обработку персональных данных в соответствии с <a href="#" class="text-primary-600 hover:underline">152-ФЗ</a></label></div>
      <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition focus:ring-4 focus:ring-primary-100 focus-ring">Зарегистрироваться</button>
    </form>
    <p class="mt-6 text-center text-sm text-gray-600">Уже есть аккаунт? <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">Войти</a></p>
  </div>
  <p class="mt-6 text-center text-xs text-gray-500">© 2024 Портал приёмной комиссии</p>
</div>
@endsection
