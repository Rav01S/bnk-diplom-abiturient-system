@extends('layouts.app')
@section('title', 'Журнал аудита | Администрирование')
@section('header', 'Журнал аудита')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
  <div>
    <h2 class="text-xl font-semibold text-gray-900">События системы</h2>
    <p class="text-gray-500 text-sm">История действий администраторов и сотрудников комиссии</p>
  </div>
  <form method="GET" action="{{ route('admin.audit-logs') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_220px_auto] lg:w-[760px]">
    <label>
      <span class="text-sm font-medium text-gray-700">Поиск</span>
      <input name="search" value="{{ request('search') }}" placeholder="Действие, объект, пользователь, IP" class="mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-lg">
    </label>
    <label>
      <span class="text-sm font-medium text-gray-700">Действие</span>
      <select name="action" class="mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white">
        <option value="">Все действия</option>
        @foreach($actions as $action)
          <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
        @endforeach
      </select>
    </label>
    <div class="flex items-end gap-2">
      <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">Найти</button>
      <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2.5 border border-gray-300 bg-white rounded-lg text-sm text-gray-600 hover:bg-gray-50">Сброс</a>
    </div>
  </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[1080px] text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Дата</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Пользователь</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Действие</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Объект</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">IP</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Детали</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($logs as $log)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-gray-600">{{ $log->created_at?->format('d.m.Y H:i') }}</td>
            <td class="px-4 py-3">
              <div class="font-medium text-gray-900">{{ $log->user?->display_name ?? 'Система' }}</div>
              <div class="mt-0.5 text-xs text-gray-500">{{ $log->user?->email ?? '—' }}</div>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-700">{{ $log->action }}</span>
            </td>
            <td class="px-4 py-3 font-medium text-gray-900">{{ $log->subject ?? '—' }}</td>
            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $log->ip_address ?? '—' }}</td>
            <td class="px-4 py-3 text-xs text-gray-600">
              @if($log->details)
                <div class="max-w-md truncate font-mono">{{ json_encode($log->details, JSON_UNESCAPED_UNICODE) }}</div>
              @else
                —
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-10 text-center text-gray-500">Событий пока нет. Новые действия будут появляться здесь.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-5">
  {{ $logs->links() }}
</div>
@endsection
