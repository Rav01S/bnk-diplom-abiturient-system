@extends('layouts.app')
@section('title', 'Настройки кампании | Администрирование')
@section('header', 'Настройки приёмной кампании')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
  <div>
    <h2 class="text-xl font-semibold text-gray-900">Управление приёмной кампанией</h2>
    <p class="text-gray-500 text-sm">Массовое управление статусами и датами программ</p>
  </div>
  <div class="flex flex-wrap gap-2">
    <form method="POST" action="{{ route('admin.campaign.open-all') }}">
      @csrf
      <button type="submit" class="px-4 py-2 bg-success hover:bg-green-700 text-white font-medium rounded-lg text-sm">Открыть все</button>
    </form>
    <form method="POST" action="{{ route('admin.campaign.close-all') }}">
      @csrf
      <button type="submit" class="px-4 py-2 bg-danger hover:bg-red-700 text-white font-medium rounded-lg text-sm">Закрыть все</button>
    </form>
  </div>
</div>

<form method="POST" action="{{ route('admin.campaign.update') }}">
  @csrf
  @method('PUT')
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[980px] text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-500">Специальность</th>
            <th class="px-4 py-3 text-center font-medium text-gray-500">Открыто</th>
            <th class="px-4 py-3 text-center font-medium text-gray-500">Начало</th>
            <th class="px-4 py-3 text-center font-medium text-gray-500">Окончание</th>
            <th class="px-4 py-3 text-center font-medium text-gray-500">Мест бюджет</th>
            <th class="px-4 py-3 text-center font-medium text-gray-500">Мест платно</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($programs as $prog)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900">{{ $prog->specialty->full_title }}</div>
                <div class="text-xs text-gray-500">{{ $prog->has_study_form ? 'Очная/Заочная' : 'Только очная' }} · {{ $prog->campaign_year }}</div>
              </td>
              <td class="px-4 py-3 text-center">
                <input type="checkbox" name="programs[{{ $prog->id }}][is_open]" value="1" {{ $prog->is_open ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded">
              </td>
              <td class="px-4 py-3 text-center">
                <input type="date" name="programs[{{ $prog->id }}][open_from]" value="{{ $prog->open_from?->format('Y-m-d') }}" class="px-2 py-1 border border-gray-300 rounded text-xs">
              </td>
              <td class="px-4 py-3 text-center">
                <input type="date" name="programs[{{ $prog->id }}][open_until]" value="{{ $prog->open_until?->format('Y-m-d') }}" class="px-2 py-1 border border-gray-300 rounded text-xs">
              </td>
              <td class="px-4 py-3 text-center font-medium">{{ $prog->plan_count }}</td>
              <td class="px-4 py-3 text-center font-medium">{{ $prog->plan_count_paid }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-gray-500">Программы пока не добавлены.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-4 flex justify-end">
    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">Сохранить настройки</button>
  </div>
</form>
@endsection
