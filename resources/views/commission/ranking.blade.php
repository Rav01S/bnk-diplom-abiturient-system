@extends('layouts.app')
@section('title', 'Ранжирование | Комиссия')
@section('header', 'Ранжирование')
@section('sidebar') @include('partials.sidebar-commission') @endsection
@section('content')
<div class="mb-6"><h2 class="text-xl font-semibold text-gray-900">Рейтинговые списки</h2><p class="text-gray-500 text-sm">Одобренные заявления, отсортированные по среднему баллу трёх предметов</p></div>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
  <form method="GET" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]"><label class="block text-xs font-medium text-gray-500 mb-1">Программа</label><select name="program_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">@foreach($programs as $p)<option value="{{ $p->id }}" {{ $selectedProgramId == $p->id ? 'selected' : '' }}>{{ $p->specialty->full_title }}</option>@endforeach</select></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Финансирование</label><select name="funding_type" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"><option value="budget" {{ $fundingType==='budget' ? 'selected' : '' }}>Бюджет</option><option value="paid" {{ $fundingType==='paid' ? 'selected' : '' }}>Платно</option></select></div>
    @if($selectedProgramId)<a href="{{ route('commission.ranking.export', ['program_id'=>$selectedProgramId, 'funding_type'=>$fundingType]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">📥 CSV</a>@endif
  </form>
</div>

@if($selectedProgram)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="px-4 py-3 border-b border-gray-200 bg-gray-50"><h3 class="font-medium text-gray-900">{{ $selectedProgram->specialty->full_title }} — {{ $fundingType==='budget' ? 'Бюджет' : 'Платно' }}</h3><p class="text-xs text-gray-500">Мест: {{ $fundingType==='budget' ? $selectedProgram->plan_count : $selectedProgram->plan_count_paid }} • В списке: {{ $ranking->count() }}</p></div>
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 w-12">№</th><th class="px-4 py-3 text-left font-medium text-gray-500">ФИО</th><th class="px-4 py-3 text-center font-medium text-gray-500">Балл 1</th><th class="px-4 py-3 text-center font-medium text-gray-500">Балл 2</th><th class="px-4 py-3 text-center font-medium text-gray-500">Балл 3</th><th class="px-4 py-3 text-center font-medium text-gray-500">Средний балл</th><th class="px-4 py-3 text-center font-medium text-gray-500">Приор.</th></tr></thead>
    <tbody class="divide-y divide-gray-200">
      @forelse($ranking as $i => $app)
      @php $plan = $fundingType==='budget' ? $selectedProgram->plan_count : $selectedProgram->plan_count_paid; @endphp
      <tr class="{{ ($i+1) <= $plan ? 'bg-green-50' : '' }} hover:bg-gray-50">
        <td class="px-4 py-3 font-bold {{ ($i+1) <= $plan ? 'text-green-700' : 'text-gray-500' }}">{{ $i+1 }}</td>
        <td class="px-4 py-3 font-medium">{{ $app->app_full_name }}</td>
        @foreach($app->scores as $score)<td class="px-4 py-3 text-center">{{ $score->score }}</td>@endforeach
        <td class="px-4 py-3 text-center font-bold">{{ number_format($app->average_score, 2, ',', '') }}</td>
        <td class="px-4 py-3 text-center">{{ $app->priority }}</td>
      </tr>
      @empty
      <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Нет одобренных заявлений для этой программы</td></tr>
      @endforelse
    </tbody></table></div>
</div>
@else
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center text-gray-500">Выберите программу для просмотра рейтинга</div>
@endif
@endsection
