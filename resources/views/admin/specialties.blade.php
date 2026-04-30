@extends('layouts.app')
@section('title', 'Специальности | Администрирование')
@section('header', 'Специальности и программы')
@section('sidebar') @include('partials.sidebar-admin') @endsection
@section('content')
<div class="mb-6 flex items-center justify-between">
  <div><h2 class="text-xl font-semibold text-gray-900">Управление специальностями</h2><p class="text-gray-500 text-sm">Справочник программ приёма</p></div>
  <button onclick="document.getElementById('addSpecModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>Добавить</button>
</div>

@foreach($specialties as $spec)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 overflow-hidden">
  <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
    <div><h3 class="font-medium text-gray-900">{{ $spec->code }} — {{ $spec->name }}</h3><p class="text-xs text-gray-500">Предметы: {{ implode(', ', $spec->subjects) }}</p></div>
    <form method="POST" action="{{ route('admin.specialties.destroy', $spec) }}" onsubmit="return confirm('Удалить специальность «{{ $spec->code }}»? Все программы и заявления по ней будут также удалены!')">@csrf @method('DELETE')<button type="submit" class="text-danger hover:text-red-700 text-xs font-medium">Удалить</button></form>
  </div>
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50/50"><tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Год</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Форма</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Бюджет</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Платно</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Статус</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Период</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Действия</th></tr></thead>
    <tbody class="divide-y divide-gray-100">
      @forelse($spec->programs as $prog)
      <tr class="hover:bg-gray-50"><td class="px-4 py-2">{{ $prog->campaign_year }}</td><td class="px-4 py-2 text-xs">{{ $prog->has_study_form ? 'Очная/Заочная' : 'Только очная' }}</td><td class="px-4 py-2 text-center">{{ $prog->plan_count }}</td><td class="px-4 py-2 text-center">{{ $prog->plan_count_paid }}</td><td class="px-4 py-2 text-center"><form method="POST" action="{{ route('admin.programs.toggle', $prog) }}" class="inline">@csrf @method('PATCH')<button type="submit" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $prog->is_open ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $prog->is_open ? '✓ Открыто' : '✕ Закрыто' }}</button></form></td><td class="px-4 py-2 text-center text-xs text-gray-500">{{ $prog->open_from?->format('d.m.Y') ?? '—' }} — {{ $prog->open_until?->format('d.m.Y') ?? '—' }}</td><td class="px-4 py-2 text-right"><form method="POST" action="{{ route('admin.programs.destroy', $prog) }}" class="inline" onsubmit="return confirm('Удалить программу {{ $prog->campaign_year }}? Все заявления по ней будут удалены!')">@csrf @method('DELETE')<button type="submit" class="text-danger text-xs">Удалить</button></form></td></tr>
      @empty
      <tr><td colspan="7" class="px-4 py-4 text-center text-gray-500 text-xs">Нет программ</td></tr>
      @endforelse
    </tbody></table></div>
  <div class="px-4 py-3 border-t border-gray-100">
    <form method="POST" action="{{ route('admin.programs.store', $spec) }}" class="flex flex-wrap gap-2 items-end">@csrf
      <input type="number" name="campaign_year" value="{{ date('Y') }}" class="w-20 px-2 py-1.5 border border-gray-300 rounded text-xs" placeholder="Год">
      <input type="number" name="plan_count" value="25" class="w-20 px-2 py-1.5 border border-gray-300 rounded text-xs" placeholder="Бюджет">
      <input type="number" name="plan_count_paid" value="10" class="w-20 px-2 py-1.5 border border-gray-300 rounded text-xs" placeholder="Платно">
      <label class="flex items-center text-xs"><input type="checkbox" name="has_study_form" value="1" checked class="w-3 h-3 mr-1">Формы</label>
      <label class="flex items-center text-xs"><input type="checkbox" name="is_open" value="1" checked class="w-3 h-3 mr-1">Открыто</label>
      <input type="date" name="open_from" value="{{ date('Y-m-d') }}" class="px-2 py-1.5 border border-gray-300 rounded text-xs">
      <input type="date" name="open_until" value="{{ date('Y-m-d', strtotime('+3 months')) }}" class="px-2 py-1.5 border border-gray-300 rounded text-xs">
      <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white rounded text-xs hover:bg-primary-700">+ Программа</button>
    </form>
  </div>
</div>
@endforeach

<div id="addSpecModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Новая специальность</h3>
    <form method="POST" action="{{ route('admin.specialties.store') }}" class="space-y-4">@csrf
      <div class="grid grid-cols-4 gap-2"><div><label class="block text-xs font-medium text-gray-700 mb-1">Код</label><input type="text" name="code" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="09.02.07"></div><div class="col-span-3"><label class="block text-xs font-medium text-gray-700 mb-1">Название</label><input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div></div>
      <div class="grid grid-cols-3 gap-2"><div><label class="block text-xs font-medium text-gray-700 mb-1">Предмет 1</label><input type="text" name="subject_1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div><div><label class="block text-xs font-medium text-gray-700 mb-1">Предмет 2</label><input type="text" name="subject_2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div><div><label class="block text-xs font-medium text-gray-700 mb-1">Предмет 3</label><input type="text" name="subject_3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div></div>
      <div class="flex justify-end gap-3"><button type="button" onclick="document.getElementById('addSpecModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Отмена</button><button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm">Создать</button></div>
    </form>
  </div>
</div>
@endsection
