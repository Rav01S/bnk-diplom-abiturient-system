@extends('layouts.app')
@section('title', 'Смена пароля | Приёмная комиссия')
@section('header', 'Смена пароля')

@section('sidebar')
  @if(auth()->user()->isAdmin())
    @include('partials.sidebar-admin')
  @elseif(auth()->user()->isCommission())
    @include('partials.sidebar-commission')
  @else
    @include('partials.sidebar-applicant')
  @endif
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Безопасность аккаунта</h2>
        <p class="text-gray-500 text-sm">Здесь вы можете изменить свой пароль для входа в систему</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Текущий пароль</label>
                <input type="password" id="current_password" name="current_password" required 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition">
                @error('current_password') <p class="mt-1 text-sm text-blue-600">{{ $message }}</p> @enderror
            </div>

            <hr class="border-gray-100">

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Новый пароль</label>
                <input type="password" id="password" name="password" required minlength="8"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition">
                @error('password') <p class="mt-1 text-sm text-blue-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">Минимум 8 символов</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Подтвердите новый пароль</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition focus:ring-4 focus:ring-primary-100">
                    Обновить пароль
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
