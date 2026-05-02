@extends('layouts.app')
@section('title', 'Статистика | Комиссия')
@section('header', 'Статистика план/факт')
@section('sidebar') @include('partials.sidebar-commission') @endsection
@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
  <div><h2 class="text-xl font-semibold text-gray-900">Статистика приёмной кампании</h2><p class="text-gray-500 text-sm">Данные за {{ $year }} год</p></div>
  <div class="flex items-center gap-3">
    <form method="GET" class="flex items-center gap-2">
      <label class="text-xs font-medium text-gray-500">Год:</label>
      <input type="number" name="campaign_year" value="{{ $year }}" onchange="this.form.submit()" class="w-24 px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white">
    </form>
    <a href="{{ route('commission.stats.export', ['campaign_year' => $year]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition">
      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
      Экспорт Excel
    </a>
  </div>
</div>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-medium text-gray-500">Специальность</th><th class="px-4 py-3 text-center font-medium text-gray-500">План (бюджет)</th><th class="px-4 py-3 text-center font-medium text-gray-500">Факт (бюджет)</th><th class="px-4 py-3 text-center font-medium text-gray-500">%</th><th class="px-4 py-3 text-center font-medium text-gray-500">План (платно)</th><th class="px-4 py-3 text-center font-medium text-gray-500">Факт (платно)</th><th class="px-4 py-3 text-center font-medium text-gray-500">На проверке</th></tr></thead>
    <tbody class="divide-y divide-gray-200">
      @foreach($stats as $s)
      @php $pct = $s['plan_budget'] > 0 ? round(($s['fact_budget'] / $s['plan_budget']) * 100) : 0; @endphp
      <tr class="hover:bg-gray-50">
        <td class="px-4 py-3"><div class="font-medium text-gray-900">{{ $s['program']->specialty->full_title }}</div><div class="text-xs text-gray-500">{{ $s['program']->has_study_form ? 'Очная/Заочная' : 'Только очная' }}</div></td>
        <td class="px-4 py-3 text-center font-medium">{{ $s['plan_budget'] }}</td>
        <td class="px-4 py-3 text-center font-bold {{ $s['fact_budget'] >= $s['plan_budget'] ? 'text-green-700' : '' }}">{{ $s['fact_budget'] }}</td>
        <td class="px-4 py-3 text-center"><div class="w-16 mx-auto"><div class="h-2 bg-gray-200 rounded-full overflow-hidden"><div class="h-full {{ $pct >= 100 ? 'bg-green-500' : ($pct >= 50 ? 'bg-primary-600' : 'bg-orange-500') }} rounded-full" style="width: {{ min($pct, 100) }}%"></div></div><span class="text-xs text-gray-500">{{ $pct }}%</span></div></td>
        <td class="px-4 py-3 text-center font-medium">{{ $s['plan_paid'] }}</td>
        <td class="px-4 py-3 text-center font-bold">{{ $s['fact_paid'] }}</td>
        <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-700">{{ $s['submitted'] }}</span></td>
      </tr>
      @endforeach
    </tbody></table></div>
</div>
@endsection
