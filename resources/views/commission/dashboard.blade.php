@extends('layouts.app')
@section('title', 'Дашборд | Приёмная комиссия БНК')
@section('header', 'Приёмная комиссия Бугурусланского нефтяного колледжа')
@section('sidebar') @include('partials.sidebar-commission') @endsection
@section('content')
<div class="mb-6"><h2 class="text-xl font-semibold text-gray-900">Рабочий стол</h2><p class="text-gray-500 text-sm">Приёмная комиссия Бугурусланского нефтяного колледжа • кампания {{ date('Y') }}</p></div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4"><div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-gray-600">На проверке</span><span class="text-primary-600 bg-primary-50 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div><p class="text-2xl font-bold">{{ $pendingCount }}</p><p class="text-xs text-gray-500">Ожидают решения</p></div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4"><div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-gray-600">Подтверждено сегодня</span><span class="text-green-600 bg-green-50 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div><p class="text-2xl font-bold text-green-700">{{ $approvedToday }}</p><p class="text-xs text-gray-500">За смену</p></div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4"><div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-gray-600">Отклонено</span><span class="text-red-600 bg-red-50 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div><p class="text-2xl font-bold text-red-700">{{ $rejectedToday }}</p><p class="text-xs text-gray-500">За смену</p></div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4"><div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-gray-600">На доработке</span><span class="text-orange-600 bg-orange-50 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg></span></div><p class="text-2xl font-bold text-orange-700">{{ $reworkCount }}</p><p class="text-xs text-gray-500">Исправляют</p></div>
</div>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
  <h3 class="font-medium text-gray-900 mb-4">Последние заявки</h3>
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50 text-left"><tr><th class="px-3 py-2 font-medium text-gray-500">ФИО</th><th class="px-3 py-2 font-medium text-gray-500">Специальность</th><th class="px-3 py-2 font-medium text-gray-500">Статус</th><th class="px-3 py-2 font-medium text-gray-500">Действие</th></tr></thead>
    <tbody class="divide-y divide-gray-100">
      @forelse($recentApps as $app)
      <tr class="hover:bg-gray-50"><td class="px-3 py-2 font-medium">{{ $app->app_full_name }}</td><td class="px-3 py-2 text-xs text-gray-600">{{ $app->program->specialty->code }} — {{ Str::limit($app->program->specialty->name, 30) }}</td><td class="px-3 py-2">@include('partials.status-badge', ['status' => $app->status])</td><td class="px-3 py-2"><a href="{{ route('commission.review', $app) }}" class="text-primary-600 hover:text-primary-700 text-xs font-medium">Открыть</a></td></tr>
      @empty
      <tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">Нет заявлений</td></tr>
      @endforelse
    </tbody></table></div>
  <a href="{{ route('commission.queue') }}" class="inline-block mt-3 text-sm text-primary-600 hover:text-primary-700 font-medium">Перейти ко всем заявлениям →</a>
</div>
@endsection
