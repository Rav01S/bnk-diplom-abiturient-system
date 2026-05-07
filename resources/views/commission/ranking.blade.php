@extends('layouts.app')
@section('title', 'Ранжирование | Комиссия')
@section('header', 'Ранжирование')
@section('sidebar') @include('partials.sidebar-commission') @endsection
@section('content')
<div class="mb-6"><h2 class="text-xl font-semibold text-gray-900">Рейтинговые списки</h2><p class="text-gray-500 text-sm">Одобренные заявления, отсортированные по среднему баллу трёх предметов</p></div>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
  <form method="GET" class="flex flex-wrap gap-3 items-end">
    <div class="w-24"><label class="block text-xs font-medium text-gray-500 mb-1">Год</label><input type="number" name="campaign_year" value="{{ $year }}" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"></div>
    <div class="flex-1 min-w-[200px]"><label class="block text-xs font-medium text-gray-500 mb-1">Программа</label><select name="program_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
      @if($programs->isEmpty())<option disabled>Нет программ на {{ $year }} год</option>@endif
      @foreach($programs as $p)<option value="{{ $p->id }}" {{ $selectedProgramId == $p->id ? 'selected' : '' }}>{{ $p->specialty->full_title }}</option>@endforeach
    </select></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Финансирование</label><select name="funding_type" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"><option value="budget" {{ $fundingType==='budget' ? 'selected' : '' }}>Бюджет</option><option value="paid" {{ $fundingType==='paid' ? 'selected' : '' }}>Хозрасчёт</option></select></div>
    @if($selectedProgramId)<a href="{{ route('commission.ranking.export', ['program_id'=>$selectedProgramId, 'funding_type'=>$fundingType]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">📥 Excel</a>@endif
  </form>
</div>

@if($selectedProgram)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="px-4 py-3 border-b border-gray-200 bg-gray-50"><h3 class="font-medium text-gray-900">{{ $selectedProgram->specialty->full_title }} — {{ $fundingType==='budget' ? 'Бюджет' : 'Хозрасчёт' }}</h3><p class="text-xs text-gray-500">Мест: {{ $fundingType==='budget' ? $selectedProgram->plan_count : $selectedProgram->plan_count_paid }} • В списке: {{ $ranking->count() }}</p></div>
  <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 w-12">№</th><th class="px-4 py-3 text-left font-medium text-gray-500">ФИО</th><th class="px-4 py-3 text-center font-medium text-gray-500">Балл 1</th><th class="px-4 py-3 text-center font-medium text-gray-500">Балл 2</th><th class="px-4 py-3 text-center font-medium text-gray-500">Балл 3</th><th class="px-4 py-3 text-center font-medium text-gray-500" title="Средний балл по аттестату">Ср. балл аттестата</th><th class="px-4 py-3 text-center font-medium text-gray-500" title="Средний балл по 3 профильным предметам">Балл по трем предметам</th><th class="px-4 py-3 text-center font-medium text-gray-500">Приор.</th></tr></thead>
    <tbody class="divide-y divide-gray-200">
      @forelse($ranking as $i => $app)
      @php $plan = $fundingType==='budget' ? $selectedProgram->plan_count : $selectedProgram->plan_count_paid; @endphp
      <tr class="{{ ($i+1) <= $plan ? 'bg-blue-50' : '' }} hover:bg-gray-50">
        <td class="px-4 py-3 font-bold {{ ($i+1) <= $plan ? 'text-blue-700' : 'text-gray-500' }}">{{ $i+1 }}</td>
        <td class="px-4 py-3 font-medium">
          {{ $app->app_full_name }}
          @if($app->is_benefit && $app->benefit_type)
            <span class="ml-1 text-[10px] font-bold px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded uppercase" title="{{ $app->benefit_label }}">
              Льгота
            </span>
          @endif
        </td>
        @foreach($app->scores as $score)<td class="px-4 py-3 text-center">{{ $score->score }}</td>@endforeach
        <td class="px-4 py-3 text-center">
          @if($app->hasPriorityBenefit())
            <span class="inline-flex items-center text-primary-700 font-bold" title="Вне конкурса">
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              6,00 ({{ number_format($app->applicant->avg_cert_score ?? 0, 2, ',', '') }})
            </span>
          @else
            {{ $app->applicant->avg_cert_score ? number_format($app->applicant->avg_cert_score, 2, ',', '') : '—' }}
          @endif
        </td>
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
