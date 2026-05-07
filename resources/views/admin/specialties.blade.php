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
    <div><h3 class="font-medium text-gray-900">{{ $spec->code }} — {{ $spec->name }} @if($spec->is_profession) <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Профессия</span> @else <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Специальность</span> @endif</h3><p class="text-xs text-gray-500">Предметы: {{ implode(', ', $spec->subjects) }}</p></div>
    <form method="POST" action="{{ route('admin.specialties.destroy', $spec) }}" onsubmit="return confirm('Удалить специальность «{{ $spec->code }}»? Все программы и заявления по ней будут также удалены!')">@csrf @method('DELETE')<button type="submit" class="text-danger hover:text-blue-700 text-xs font-medium">Удалить</button></form>
  </div>
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50/50"><tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Год</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Бюджет</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Платно</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Форма</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Статус</th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Период</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Действия</th></tr></thead>
    <tbody class="divide-y divide-gray-100">
      @forelse($spec->programs as $prog)
      <tr class="hover:bg-gray-50">
        <td class="px-4 py-2 font-medium">{{ $prog->campaign_year }}</td>
        <td class="px-4 py-2 text-center">{{ $prog->plan_count }}</td>
        <td class="px-4 py-2 text-center">{{ $prog->plan_count_paid }}</td>
        <td class="px-4 py-2 text-center text-xs text-gray-600">{{ $prog->has_study_form ? 'Очно / Заочно' : 'Очно' }}</td>
        <td class="px-4 py-2 text-center">
          <form method="POST" action="{{ route('admin.programs.toggle', $prog) }}" class="inline">@csrf @method('PATCH')
            <button type="submit" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $prog->is_open ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
              {{ $prog->is_open ? '✓ Открыто' : '✕ Закрыто' }}
            </button>
          </form>
        </td>
        <td class="px-4 py-2 text-center text-xs text-gray-500">{{ $prog->open_from?->format('d.m.Y') ?? '—' }} — {{ $prog->open_until?->format('d.m.Y') ?? '—' }}</td>
        <td class="px-4 py-2 text-right space-x-2">
          <button onclick="editProgramModal({
            id: {{ $prog->id }},
            year: {{ $prog->campaign_year }},
            plan: {{ $prog->plan_count }},
            plan_paid: {{ $prog->plan_count_paid }},
            has_study_form: {{ $prog->has_study_form ? 1 : 0 }},
            is_open: {{ $prog->is_open ? 1 : 0 }},
            from: '{{ $prog->open_from?->toDateString() }}',
            until: '{{ $prog->open_until?->toDateString() }}'
          }, '{{ $spec->full_title }}')" class="text-primary-600 hover:text-primary-700 text-xs font-medium">Ред.</button>
          <form method="POST" action="{{ route('admin.programs.destroy', $prog) }}" class="inline" onsubmit="return confirm('Удалить программу {{ $prog->campaign_year }}? Все заявления по ней будут удалены!')">@csrf @method('DELETE')<button type="submit" class="text-danger text-xs">Удалить</button></form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="px-4 py-4 text-center text-gray-500 text-xs">Нет программ</td></tr>
      @endforelse
    </tbody></table></div>
  <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/30">
    <button onclick="openProgramModal({{ $spec->id }}, '{{ $spec->full_title }}')" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-medium hover:bg-gray-50 transition">
      <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
      Добавить программу обучения
    </button>
  </div>
</div>
@endforeach

<!-- Модалка специальности -->
<div id="addSpecModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Новая специальность</h3>
    <form method="POST" action="{{ route('admin.specialties.store') }}" class="space-y-4">@csrf
      <div class="grid grid-cols-4 gap-2"><div><label class="block text-xs font-medium text-gray-700 mb-1">Код</label><input type="text" name="code" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="09.02.07"></div><div class="col-span-3"><label class="block text-xs font-medium text-gray-700 mb-1">Название</label><input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div></div>
      <div class="grid grid-cols-3 gap-2"><div><label class="block text-xs font-medium text-gray-700 mb-1">Предмет 1</label><input type="text" name="subject_1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div><div><label class="block text-xs font-medium text-gray-700 mb-1">Предмет 2</label><input type="text" name="subject_2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div><div><label class="block text-xs font-medium text-gray-700 mb-1">Предмет 3</label><input type="text" name="subject_3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div></div>
      <div><label class="flex items-center"><input type="checkbox" name="is_profession" value="1" class="w-4 h-4 text-primary-600 rounded mr-2"><span class="text-sm font-medium text-gray-700">Это профессия (не специальность)</span></label></div>
      <div class="flex justify-end gap-3"><button type="button" onclick="document.getElementById('addSpecModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Отмена</button><button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm">Создать</button></div>
    </form>
  </div>
</div>

<!-- Модалка программы -->
<div id="programModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
    <h3 id="programModalTitle" class="text-lg font-medium text-gray-900 mb-4">Добавить программу</h3>
    <form id="programForm" method="POST" class="space-y-4">@csrf
      <div class="grid grid-cols-3 gap-4">
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Год приёма</label><input type="number" name="campaign_year" required value="{{ date('Y') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Мест (бюджет)</label><input type="number" name="plan_count" required value="25" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Мест (платно)</label><input type="number" name="plan_count_paid" required value="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div>
      </div>
      <div class="flex gap-6">
        <label class="flex items-center cursor-pointer"><input type="checkbox" name="is_open" value="1" checked class="w-4 h-4 text-primary-600 rounded mr-2"><span class="text-sm text-gray-700">Открыть приём</span></label>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-2">Форма обучения</label>
        <select name="has_study_form" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
          <option value="0">Очно</option>
          <option value="1">Очно и заочно</option>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Дата открытия (необяз.)</label><input type="date" name="open_from" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div>
        <div><label class="block text-xs font-medium text-gray-700 mb-1">Дата закрытия (необяз.)</label><input type="date" name="open_until" value="{{ date('Y-m-d', strtotime('+3 months')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></div>
      </div>
      <div class="flex justify-end gap-3 pt-2"><button type="button" onclick="document.getElementById('programModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Отмена</button><button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm">Сохранить</button></div>
    </form>
  </div>
</div>

<script>
function openProgramModal(specId, title) {
  const modal = document.getElementById('programModal');
  const form = document.getElementById('programForm');
  const titleEl = document.getElementById('programModalTitle');
  
  titleEl.innerText = 'Добавить программу: ' + title;
  form.action = '/admin/specialties/' + specId + '/programs';
  
  // Сброс полей
  form.querySelector('[name="campaign_year"]').value = new Date().getFullYear();
  form.querySelector('[name="plan_count"]').value = 25;
  form.querySelector('[name="plan_count_paid"]').value = 10;
  form.querySelector('[name="has_study_form"]').value = '0';
  form.querySelector('[name="is_open"]').checked = true;
  form.querySelector('[name="open_from"]').value = new Date().toISOString().split('T')[0];
  
  const dateUntil = new Date();
  dateUntil.setMonth(dateUntil.getMonth() + 3);
  form.querySelector('[name="open_until"]').value = dateUntil.toISOString().split('T')[0];

  // Удаляем метод PUT если он был
  const methodInput = form.querySelector('[name="_method"]');
  if (methodInput) methodInput.remove();

  modal.classList.remove('hidden');
}

function editProgramModal(prog, specTitle) {
  const modal = document.getElementById('programModal');
  const form = document.getElementById('programForm');
  const titleEl = document.getElementById('programModalTitle');
  
  titleEl.innerText = 'Редактировать программу: ' + specTitle + ' (' + prog.year + ')';
  form.action = '/admin/programs/' + prog.id;
  
  // Добавляем метод PUT
  if (!form.querySelector('[name="_method"]')) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = '_method';
    input.value = 'PUT';
    form.appendChild(input);
  }
  
  form.querySelector('[name="campaign_year"]').value = prog.year;
  form.querySelector('[name="plan_count"]').value = prog.plan;
  form.querySelector('[name="plan_count_paid"]').value = prog.plan_paid;
  form.querySelector('[name="has_study_form"]').value = prog.has_study_form ? '1' : '0';
  form.querySelector('[name="is_open"]').checked = !!prog.is_open;
  form.querySelector('[name="open_from"]').value = prog.from || '';
  form.querySelector('[name="open_until"]').value = prog.until || '';

  modal.classList.remove('hidden');
}
</script>
@endsection
