@extends('layouts.app')
@section('title', 'Заявления | Приёмная комиссия БНК')
@section('header', 'Мои заявления')
@section('sidebar') @include('partials.sidebar-applicant') @endsection
@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
  <div><h2 class="text-xl font-semibold text-gray-900">Список заявлений</h2><p class="text-gray-500 text-sm">Управляйте поданными заявлениями</p></div>
  <a href="{{ route('applicant.applications.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>Новое заявление</a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
  <form method="GET" class="flex flex-wrap gap-3">
    <div class="flex-1 min-w-[200px]"><label class="block text-xs font-medium text-gray-500 mb-1">Поиск</label><input type="search" name="search" value="{{ request('search') }}" placeholder="Специальность, №..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600"></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Статус</label><select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"><option value="">Все статусы</option>@foreach(['draft'=>'Черновик','submitted'=>'На проверке','approved'=>'Принято','rejected'=>'Отклонено','rework_needed'=>'На доработке','cancelled'=>'Отменено'] as $val=>$label)<option value="{{ $val }}" {{ request('status')===$val ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Сортировка</label><select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"><option value="date_desc" {{ request('sort')==='date_desc' ? 'selected' : '' }}>Сначала новые</option><option value="date_asc" {{ request('sort')==='date_asc' ? 'selected' : '' }}>Сначала старые</option><option value="priority_asc" {{ request('sort')==='priority_asc' ? 'selected' : '' }}>По приоритету ↑</option></select></div>
  </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-medium text-gray-500">№</th><th class="px-4 py-3 text-left font-medium text-gray-500">Специальность</th><th class="px-4 py-3 text-left font-medium text-gray-500">Форма/Финанс.</th><th class="px-4 py-3 text-left font-medium text-gray-500">Приоритет</th><th class="px-4 py-3 text-left font-medium text-gray-500">Статус</th><th class="px-4 py-3 text-left font-medium text-gray-500">Дата</th><th class="px-4 py-3 text-right font-medium text-gray-500">Действия</th></tr></thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($applications as $app)
        <tr class="hover:bg-gray-50 transition">
          <td class="px-4 py-3 font-mono text-xs text-gray-600">#{{ $app->id }}</td>
          <td class="px-4 py-3"><div class="font-medium text-gray-900 truncate max-w-xs">{{ $app->program->specialty->full_title }}</div><div class="text-xs text-gray-500">{{ $app->doc_type === 'original' ? 'Оригинал' : 'Копия' }}{{ $app->is_benefit ? ' • Льгота' : '' }}{{ $app->needs_dorm ? ' • Общежитие' : '' }}</div></td>
          <td class="px-4 py-3 text-xs text-gray-600">{{ $app->study_form === 'full_time' ? 'Очная' : 'Заочная' }}<br><span class="{{ $app->funding_type === 'budget' ? 'text-green-700' : 'text-orange-700' }}">{{ $app->funding_type === 'budget' ? 'Бюджет' : 'Платно' }}</span></td>
          <td class="px-4 py-3"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-xs font-medium text-gray-700">{{ $app->priority }}</span></td>
          <td class="px-4 py-3">@include('partials.status-badge', ['status' => $app->status])</td>
          <td class="px-4 py-3 text-gray-600 text-xs">{{ $app->created_at->format('d.m.Y') }}</td>
          <td class="px-4 py-3 text-right space-x-1">
            <a href="{{ route('applicant.applications.show', $app) }}" class="text-primary-600 hover:text-primary-700 text-xs font-medium">Просмотр</a>
            @if($app->isEditable())<a href="{{ route('applicant.applications.edit', $app) }}" class="text-warning-600 hover:text-warning-700 text-xs font-medium ml-1">Ред.</a>@endif
            @if($app->isCancellable())<form method="POST" action="{{ route('applicant.applications.cancel', $app) }}" class="inline" onsubmit="return confirm('Отменить заявление?')">@csrf @method('PATCH')<button type="submit" class="text-danger hover:text-red-700 text-xs font-medium ml-1">Отменить</button></form>@endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Заявления не найдены</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t border-gray-200">{{ $applications->withQueryString()->links() }}</div>
</div>
@endsection
