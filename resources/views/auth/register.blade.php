@extends('layouts.auth')
@section('title', 'Регистрация | Приёмная комиссия БНК')
@section('content')
<div class="w-full max-w-lg">
  <div class="text-center mb-6">
    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-white/50">
      <img src="{{ asset('logo.gif') }}" alt="Логотип БНК" class="h-14 w-14 object-contain">
    </div>
    <h1 class="text-2xl font-bold text-white">Регистрация абитуриента</h1>
    <p class="text-primary-100 mt-1">Приёмная комиссия ГАПОУ Бугурусланский нефтяной колледж</p>
  </div>
  <div class="auth-card bg-white rounded-lg shadow p-6 sm:p-8 ring-1 ring-white/70">
    @if($errors->any())
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div><label for="last_name" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Фамилия</label><input type="text" id="last_name" name="last_name" required maxlength="100" pattern="[А-Яа-яЁёA-Za-z\s-]+" value="{{ old('last_name') }}" class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="Иванов"></div>
        <div><label for="first_name" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Имя</label><input type="text" id="first_name" name="first_name" required maxlength="100" pattern="[А-Яа-яЁёA-Za-z\s-]+" value="{{ old('first_name') }}" class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="Иван"></div>
        <div><label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Отчество</label><input type="text" id="middle_name" name="middle_name" maxlength="100" pattern="[А-Яа-яЁёA-Za-z\s-]+" value="{{ old('middle_name') }}" class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="Иванович"></div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label for="email" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Email</label><input type="email" id="email" name="email" required maxlength="255" pattern="^[^\s@]+@[^\s@]+\.[A-Za-zА-Яа-яЁё]{2,}$" value="{{ old('email') }}" class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="user@example.com" autocomplete="email"></div>
        <div><label for="password" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Пароль</label><input type="password" id="password" name="password" required minlength="8" pattern="^(?=.*\d).{8,}$" class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="••••••••" autocomplete="new-password"></div>
      </div>
      <div><label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Подтвердите пароль</label><input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring" placeholder="••••••••" autocomplete="new-password" aria-describedby="passwordMatch"><p id="passwordMatch" class="mt-1 hidden text-xs text-blue-700">Пароли не совпадают</p></div>
      <div class="flex items-start"><input type="checkbox" id="consent" name="consent" required class="mt-1 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-600 focus-ring"><label for="consent" class="ml-2 text-sm text-gray-600">Я даю согласие на обработку персональных данных в соответствии с <a href="https://kapmed.ru/upload/iblock/739/bm38k7g738luplja952kid7bu4n0mdpb.pdf" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:underline">152-ФЗ</a></label></div>
      <button type="submit" class="auth-chevron-button w-full bg-sky-500 hover:bg-sky-600 text-white font-medium py-2.5 px-4 rounded-lg transition focus:ring-4 focus:ring-sky-100 focus-ring">
        <span class="relative">Зарегистрироваться</span>
        <span class="auth-chevron-track" aria-hidden="true">
          <span class="auth-chevron"></span>
          <span class="auth-chevron"></span>
          <span class="auth-chevron"></span>
          <span class="auth-chevron"></span>
          <span class="auth-chevron"></span>
        </span>
      </button>
    </form>
    <p class="mt-6 text-center text-sm text-gray-600">Уже есть аккаунт? <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">Войти</a></p>
  </div>
  <p class="mt-6 text-center text-xs text-primary-100">© {{ date('Y') }} ГАПОУ Бугурусланский нефтяной колледж</p>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const passwordInput = document.getElementById('password');
  const confirmationInput = document.getElementById('password_confirmation');
  const emailInput = document.getElementById('email');
  const matchMessage = document.getElementById('passwordMatch');

  function updatePasswordState() {
    const password = passwordInput?.value || '';
    const confirmation = confirmationInput?.value || '';
    const isLongEnough = password.length >= 8;
    const hasNumber = /\d/.test(password);
    const isConfirmed = confirmation === '' || password === confirmation;

    passwordInput?.classList.toggle('border-blue-500', password !== '' && (!isLongEnough || !hasNumber));
    confirmationInput?.classList.toggle('border-blue-500', !isConfirmed);
    matchMessage?.classList.toggle('hidden', isConfirmed);
  }

  passwordInput?.addEventListener('input', updatePasswordState);
  confirmationInput?.addEventListener('input', updatePasswordState);
  updatePasswordState();

  function updateEmailState() {
    const email = emailInput?.value.trim() || '';
    const isValid = email === '' || /^[^\s@]+@[^\s@]+\.[A-Za-zА-Яа-яЁё]{2,}$/.test(email);

    emailInput?.classList.toggle('border-blue-500', !isValid);
  }

  emailInput?.addEventListener('input', updateEmailState);
  updateEmailState();
});
</script>
@endsection
