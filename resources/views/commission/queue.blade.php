@extends('layouts.app')
@section('title', 'Очередь | Комиссия')
@section('header', 'Очередь на проверку')
@section('sidebar') @include('partials.sidebar-commission') @endsection
@section('content')
<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
  <div><h2 class="text-xl font-semibold text-gray-900">Заявления на проверку</h2><p class="text-sm text-gray-500">{{ $applications->total() }} заявлений ожидают решения</p></div>
  <form method="GET" class="flex gap-2"><input type="search" name="search" value="{{ request('search') }}" placeholder="ФИО, №, специальность..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600 min-w-[220px]"><button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm hover:bg-primary-700">Найти</button></form>
</div>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-medium text-gray-500">№</th><th class="px-4 py-3 text-left font-medium text-gray-500">ФИО</th><th class="px-4 py-3 text-left font-medium text-gray-500">Специальность</th><th class="px-4 py-3 text-left font-medium text-gray-500">Ревизия</th><th class="px-4 py-3 text-left font-medium text-gray-500">Дата</th><th class="px-4 py-3 text-right font-medium text-gray-500">Действие</th></tr></thead>
    <tbody class="divide-y divide-gray-200">
      @forelse($applications as $app)
      <tr class="hover:bg-gray-50 transition"><td class="px-4 py-3 font-mono text-xs">#{{ $app->id }}</td><td class="px-4 py-3 font-medium">{{ $app->app_full_name }}</td><td class="px-4 py-3 text-xs text-gray-600">{{ $app->program->specialty->full_title }}</td><td class="px-4 py-3"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-xs">{{ $app->revision }}</span></td><td class="px-4 py-3 text-xs text-gray-600">{{ $app->created_at->format('d.m.Y H:i') }}</td><td class="px-4 py-3 text-right"><a href="{{ route('commission.review', $app) }}" class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white rounded text-xs font-medium hover:bg-primary-700">Проверить</a></td></tr>
      @empty
      <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Очередь пуста — все заявления проверены! 🎉</td></tr>
      @endforelse
    </tbody></table></div>
  <div class="px-4 py-3 border-t border-gray-200">{{ $applications->withQueryString()->links() }}</div>
</div>
@endsection
