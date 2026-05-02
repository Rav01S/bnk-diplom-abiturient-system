@extends('layouts.app')
@section('title', 'Статистика | Комиссия')
@section('header', 'Статистика план/факт')
@section('sidebar') @include('partials.sidebar-commission') @endsection
@section('content')
<div class="mb-6 flex items-center justify-between">
  <div><h2 class="text-xl font-semibold text-gray-900">Статистика приёмной кампании</h2><p class="text-gray-500 text-sm">Данные по всем программам</p></div>
  <a href="{{ route('commission.stats.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">📥 Экспорт Excel</a>
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
