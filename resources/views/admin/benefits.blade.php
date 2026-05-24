@extends('layouts.app')
@section('title', 'Льготы | Администрирование')
@section('header', 'Льготы и приоритеты')
@section('sidebar') @include('partials.sidebar-admin') @endsection
@section('content')

@if(session('error'))
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">{{ session('error') }}</div>
@endif

<div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
  <div>
    <h2 class="text-xl font-semibold text-gray-900">Справочник льгот</h2>
    <p class="text-gray-500 text-sm">Настройка правил влияния льгот на ранжирование заявлений.</p>
  </div>
  <button type="button" onclick="openBenefitModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-sm">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
    Добавить льготу
  </button>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[820px] text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left font-medium text-gray-500 w-16">Порядок</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500">Название</th>
          <th class="px-4 py-3 text-center font-medium text-gray-500">Влияет на порядок</th>
          <th class="px-4 py-3 text-center font-medium text-gray-500">Влияние на балл</th>
          <th class="px-4 py-3 text-center font-medium text-gray-500">Заявления</th>
          <th class="px-4 py-3 text-center font-medium text-gray-500">Статус</th>
          <th class="px-4 py-3 text-right font-medium text-gray-500">Действия</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($benefits as $benefit)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 text-gray-500">{{ $benefit->sort_order }}</td>
            <td class="px-4 py-3 font-medium text-gray-900">{{ $benefit->label }}</td>
            <td class="px-4 py-3 text-center">
              @if($benefit->gives_priority)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">Да</span>
              @else
                <span class="text-gray-400 text-xs">—</span>
              @endif
            </td>
            <td class="px-4 py-3 text-center text-sm">
              @if($benefit->boost_mode === \App\Models\Benefit::BoostReplace)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">подменить на {{ number_format($benefit->boost_value, 2, ',', '') }}</span>
              @elseif($benefit->boost_mode === \App\Models\Benefit::BoostAdd)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">прибавить +{{ number_format($benefit->boost_value, 2, ',', '') }}</span>
              @else
                <span class="text-gray-400 text-xs">не влияет</span>
              @endif
            </td>
            <td class="px-4 py-3 text-center text-gray-600">{{ $benefit->applications_count }}</td>
            <td class="px-4 py-3 text-center">
              <form method="POST" action="{{ route('admin.benefits.toggle', $benefit) }}" class="inline">@csrf @method('PATCH')
                <button type="submit" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $benefit->is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                  {{ $benefit->is_active ? '✓ Активна' : '✕ Отключена' }}
                </button>
              </form>
            </td>
            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
              <button type="button" onclick='editBenefitModal(@json($benefit))' class="text-primary-600 hover:text-primary-700 text-xs font-medium">Ред.</button>
              <form method="POST" action="{{ route('admin.benefits.destroy', $benefit) }}" class="inline" onsubmit="return confirm('Удалить льготу «{{ $benefit->label }}»?')">@csrf @method('DELETE')<button type="submit" class="text-danger text-xs">Удалить</button></form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">Льготы пока не настроены.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div id="benefitModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
    <h3 id="benefitModalTitle" class="text-lg font-medium text-gray-900 mb-4">Новая льгота</h3>
    <form id="benefitForm" method="POST" class="space-y-4">@csrf
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="sm:col-span-3">
          <label class="block text-xs font-medium text-gray-700 mb-1">Название</label>
          <input type="text" name="label" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Порядок (0–100)</label>
          <input type="number" name="sort_order" min="0" max="100" value="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
      </div>

      <div class="border-t border-gray-100 pt-4">
        <label class="block text-xs font-medium text-gray-700 mb-2">Влияние на средний балл аттестата</label>
        <div class="space-y-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="boost_mode" value="" class="w-4 h-4 text-primary-600 boost-mode-radio">
            <span class="text-sm text-gray-800">Не влияет</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="boost_mode" value="replace" class="w-4 h-4 text-primary-600 boost-mode-radio">
            <span class="text-sm text-gray-800">Подменить балл на указанное значение</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="boost_mode" value="add" class="w-4 h-4 text-primary-600 boost-mode-radio">
            <span class="text-sm text-gray-800">Прибавить к баллу указанное значение</span>
          </label>
        </div>
        <div id="boostValueRow" class="mt-3 ml-6 hidden">
          <label class="block text-xs font-medium text-gray-700 mb-1">Значение</label>
          <input type="number" name="boost_value" step="0.01" min="0" max="6" value="6.00" class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
      </div>

      <div class="border-t border-gray-100 pt-4 space-y-2">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="gives_priority" value="1" class="w-4 h-4 text-primary-600 rounded">
          <span class="text-sm text-gray-800">Влияет на порядок списка (вне конкурса)</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" id="isActiveToggle" class="w-4 h-4 text-primary-600 rounded" checked>
          <span class="text-sm text-gray-800">Активна (доступна абитуриентам)</span>
        </label>
      </div>

      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="document.getElementById('benefitModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Отмена</button>
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm">Сохранить</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleBoostValue() {
  const selected = document.querySelector('#benefitForm input[name="boost_mode"]:checked');
  const mode = selected ? selected.value : '';
  document.getElementById('boostValueRow').classList.toggle('hidden', mode === '');
  if (mode !== '') {
    const valueInput = document.querySelector('#benefitForm [name="boost_value"]');
    if (!valueInput.value || valueInput.value === '0') {
      valueInput.value = mode === 'replace' ? '6.00' : '0.50';
    }
  }
}

document.querySelectorAll('.boost-mode-radio').forEach(el => el.addEventListener('change', toggleBoostValue));

function resetBenefitForm() {
  const form = document.getElementById('benefitForm');
  form.reset();
  const method = form.querySelector('[name="_method"]');
  if (method) method.remove();
  document.getElementById('isActiveToggle').checked = true;
  form.querySelector('[name="boost_mode"][value=""]').checked = true;
  form.querySelector('[name="sort_order"]').value = 50;
  form.querySelector('[name="boost_value"]').value = '6.00';
}

function openBenefitModal() {
  resetBenefitForm();
  document.getElementById('benefitModalTitle').innerText = 'Новая льгота';
  document.getElementById('benefitForm').action = '{{ route("admin.benefits.store") }}';
  toggleBoostValue();
  document.getElementById('benefitModal').classList.remove('hidden');
}

function editBenefitModal(benefit) {
  resetBenefitForm();
  document.getElementById('benefitModalTitle').innerText = 'Редактирование: ' + benefit.label;
  const form = document.getElementById('benefitForm');
  form.action = '/admin/benefits/' + benefit.id;

  const method = document.createElement('input');
  method.type = 'hidden';
  method.name = '_method';
  method.value = 'PUT';
  form.appendChild(method);

  form.querySelector('[name="label"]').value = benefit.label ?? '';
  form.querySelector('[name="sort_order"]').value = benefit.sort_order ?? 0;
  form.querySelector('[name="gives_priority"]').checked = !!benefit.gives_priority;

  const mode = benefit.boost_mode || '';
  const radio = form.querySelector('[name="boost_mode"][value="' + mode + '"]');
  if (radio) radio.checked = true;
  form.querySelector('[name="boost_value"]').value = benefit.boost_value ?? '6.00';
  document.getElementById('isActiveToggle').checked = !!benefit.is_active;

  toggleBoostValue();
  document.getElementById('benefitModal').classList.remove('hidden');
}
</script>
@endsection
