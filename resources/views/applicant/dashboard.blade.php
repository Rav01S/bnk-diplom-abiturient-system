@extends('layouts.app')
@section('title', 'Дашборд | Портал приёмной комиссии')
@section('header', 'Дашборд абитуриента')
@section('sidebar') @include('partials.sidebar-applicant') @endsection
@section('content')
<div class="mb-6">
  <h2 class="text-xl font-semibold text-gray-900 mb-1">Добро пожаловать, {{ auth()->user()->applicant->first_name ?? 'Абитуриент' }}! 👋</h2>
  <p class="text-gray-500 text-sm">Приёмная кампания 2024 • Очно / Заочно • Бюджет / Платно</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm font-medium text-gray-600">Заявления</span>
      <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <div class="flex items-end justify-between">
      <div><span class="text-2xl font-bold text-gray-900">{{ $activeCount }}</span><span class="text-gray-500 text-sm">/ 5</span></div>
      <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden"><div class="h-full bg-primary-600 rounded-full transition-all" style="width: {{ ($activeCount / 5) * 100 }}%"></div></div>
    </div>
    <p class="mt-2 text-xs text-gray-500">Осталось: <span class="font-medium text-primary-600">{{ 5 - $activeCount }}</span></p>
  </div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-gray-600">На проверке</span><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Активно</span></div>
    <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p><p class="mt-1 text-xs text-gray-500">Заявлений ожидает решения</p>
  </div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-gray-600">Принято</span><svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
    <p class="text-2xl font-bold text-gray-900">{{ $approvedCount }}</p><p class="mt-1 text-xs text-gray-500">Успешно поданных заявлений</p>
  </div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-gray-600">Требует внимания</span><svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
    <p class="text-2xl font-bold text-gray-900">{{ $attentionCount }}</p><p class="mt-1 text-xs text-gray-500">Нужна доработка или есть отказ</p>
  </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
  <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
    <h3 class="font-medium text-gray-900">Ваши заявления</h3>
    <a href="{{ route('applicant.applications') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Все заявления →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left font-medium text-gray-500">№</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Специальность</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Форма/Финанс.</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Приоритет</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Статус</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Дата</th>
          <th class="px-4 py-3 text-right font-medium text-gray-500">Действия</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($applications as $app)
        <tr class="hover:bg-gray-50 transition">
          <td class="px-4 py-3 font-mono text-xs text-gray-600">#{{ $app->id }}</td>
          <td class="px-4 py-3"><div class="font-medium text-gray-900 truncate max-w-xs">{{ $app->program->specialty->full_title }}</div></td>
          <td class="px-4 py-3 text-xs text-gray-600">{{ $app->study_form === 'full_time' ? 'Очная' : 'Заочная' }}<br><span class="{{ $app->funding_type === 'budget' ? 'text-green-700' : 'text-orange-700' }}">{{ $app->funding_type === 'budget' ? 'Бюджет' : 'Платно' }}</span></td>
          <td class="px-4 py-3"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-xs font-medium text-gray-700">{{ $app->priority }}</span></td>
          <td class="px-4 py-3">@include('partials.status-badge', ['status' => $app->status])</td>
          <td class="px-4 py-3 text-gray-600 text-xs">{{ $app->created_at->format('d.m.Y') }}</td>
          <td class="px-4 py-3 text-right"><a href="{{ route('applicant.applications.show', $app) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Просмотр</a></td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Заявления пока не поданы</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-6 text-center">
  <a href="{{ route('applicant.applications.create') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition focus:ring-4 focus:ring-primary-100 focus-ring">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
    Подать новое заявление
  </a>
  <p class="mt-2 text-xs text-gray-500">Максимум 5 заявлений в приёмную кампанию</p>
</div>
@endsection
