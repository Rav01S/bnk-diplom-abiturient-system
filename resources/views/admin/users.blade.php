@extends('layouts.app')
@section('title', 'Сотрудники | Администрирование')
@section('header', 'Управление сотрудниками')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@php
  $isCreating = request()->boolean('create');
  $editingId = request()->integer('edit');
  $roleLabels = [
    'admin' => 'Администратор',
    'commission' => 'Сотрудник комиссии',
    'applicant' => 'Абитуриент'
  ];
@endphp

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
  <div>
    <h2 class="text-xl font-semibold text-gray-900">Управление пользователями</h2>
    <p class="text-gray-500 text-sm">Активация, деактивация и редактирование аккаунтов</p>
  </div>
  @if($tab === 'staff')
  <a href="{{ route('admin.users', ['create' => 1, 'tab' => 'staff']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
    Добавить сотрудника
  </a>
  @endif
</div>

{{-- Вкладки --}}
<div class="mb-6 border-b border-gray-200">
  <nav class="-mb-px flex space-x-8" aria-label="Tabs">
    <a href="{{ route('admin.users', ['tab' => 'staff']) }}" 
       class="{{ $tab === 'staff' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
      Сотрудники
    </a>
    <a href="{{ route('admin.users', ['tab' => 'applicants']) }}" 
       class="{{ $tab === 'applicants' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
      Абитуриенты
    </a>
  </nav>
</div>

@if($isCreating && $tab === 'staff')
<section class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-5">
  <div class="mb-4 flex items-center justify-between gap-4">
    <h3 class="text-lg font-semibold text-gray-900">Новый сотрудник</h3>
    <a href="{{ route('admin.users', ['tab' => 'staff']) }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Отмена</a>
  </div>
  <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-5">
    @csrf
    <label class="lg:col-span-2">
      <span class="text-sm font-medium text-gray-700">ФИО</span>
      <input name="full_name" value="{{ old('full_name') }}" class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg" placeholder="Иванов Иван Иванович">
    </label>
    <label>
      <span class="text-sm font-medium text-gray-700">Email</span>
      <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg" placeholder="user@portal.ru">
    </label>
    <label>
      <span class="text-sm font-medium text-gray-700">Роль</span>
      <select name="role" class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white">
        <option value="commission" @selected(old('role', 'commission') === 'commission')>Сотрудник комиссии</option>
        <option value="admin" @selected(old('role') === 'admin')>Администратор</option>
      </select>
    </label>
    <label>
      <span class="text-sm font-medium text-gray-700">Пароль</span>
      <input name="password" value="{{ old('password', 'password123') }}" required minlength="8" class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg">
    </label>
    <div class="lg:col-span-5">
      <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">Сохранить</button>
    </div>
  </form>
</section>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[980px] text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left font-medium text-gray-500">ID</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Пользователь</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Роль</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Регистрация</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Статус</th>
          <th class="px-4 py-3 text-right font-medium text-gray-500">Действия</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($users as $user)
          @php $isActive = $user->is_active; @endphp
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-mono text-xs text-gray-500">#{{ $user->id }}</td>
            <td class="px-4 py-3">
              <div class="font-medium text-gray-900">{{ $user->email }}</div>
              <div class="mt-0.5 text-xs text-gray-500">{{ $user->display_name }}</div>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-secondary-100 text-secondary-700' : ($user->role === 'commission' ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-700') }}">
                {{ $roleLabels[$user->role] ?? $user->role }}
              </span>
            </td>
            <td class="px-4 py-3 text-gray-600">{{ $user->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $isActive ? 'Активен' : 'Деактивирован' }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('admin.users', ['edit' => $user->id, 'tab' => $tab]) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Изменить</a>
                @if($user->id !== auth()->id())
                  <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs font-medium {{ $isActive ? 'text-danger hover:text-red-700' : 'text-success hover:text-green-700' }}">
                      {{ $isActive ? 'Деактивировать' : 'Активировать' }}
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Удалить пользователя?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-medium text-danger hover:text-red-700">Удалить</button>
                  </form>
                @else
                  <span class="text-xs text-gray-400">Это вы</span>
                @endif
              </div>
            </td>
          </tr>

          @if($editingId === $user->id)
            <tr class="bg-primary-50/40">
              <td colspan="6" class="px-4 py-4">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid grid-cols-1 gap-4 lg:grid-cols-5">
                  @csrf
                  @method('PUT')
                  <label class="lg:col-span-2">
                    <span class="text-sm font-medium text-gray-700">ФИО</span>
                    @php
                      $currentName = $user->full_name ?: ($user->isApplicant() && $user->applicant ? $user->display_name : '');
                      if ($currentName === $user->email) $currentName = '';
                    @endphp
                    <input name="full_name" value="{{ old('full_name', $currentName) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                  </label>
                  <label class="lg:col-span-2">
                    <span class="text-sm font-medium text-gray-700">Email</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                  </label>
                  <label>
                    <span class="text-sm font-medium text-gray-700">Роль</span>
                    @if($user->role === 'applicant')
                      <div class="mt-2 text-sm text-gray-600 font-medium">Абитуриент</div>
                      <input type="hidden" name="role" value="applicant">
                    @else
                      <select name="role" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
                        <option value="commission" @selected(old('role', $user->role) === 'commission')>Сотрудник комиссии</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Администратор</option>
                      </select>
                    @endif
                  </label>
                  <label class="lg:col-span-2">
                    <span class="text-sm font-medium text-gray-700">Новый пароль</span>
                    <input name="password" type="password" minlength="8" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Оставьте пустым, если не меняете">
                  </label>
                  <div class="flex items-end gap-3 lg:col-span-3">
                    <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">Сохранить изменения</button>
                    <a href="{{ route('admin.users', ['tab' => $tab]) }}" class="px-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-white">Отмена</a>
                  </div>
                </form>
              </td>
            </tr>
          @endif
        @empty
          <tr>
            <td colspan="6" class="px-4 py-10 text-center text-gray-500">Пользователи пока не добавлены.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
  <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
    {{ $users->links() }}
  </div>
  @endif
</div>
@endsection
