@extends('layouts.app')
@section('title', 'Редактировать заявление | Приёмная комиссия БНК')
@section('header', 'Редактирование заявления №' . $application->id)
@section('sidebar') @include('partials.sidebar-applicant') @endsection
@section('content')
<div class="mb-4"><a href="{{ route('applicant.applications.show', $application) }}" class="text-sm text-gray-600 hover:text-gray-900">← Назад к заявлению</a></div>
<div class="mb-4 p-3 bg-primary-50 border border-primary-100 rounded-lg text-sm text-primary-700">ℹ️ При сохранении номер ревизии будет увеличен (текущая: {{ $application->revision }}), данные профиля обновятся в снапшоте.</div>

<form method="POST" action="{{ route('applicant.applications.update', $application) }}" enctype="multipart/form-data" class="space-y-6" novalidate>
  @csrf @method('PUT')
  <input type="hidden" name="program_id" value="{{ $application->program_id }}">

  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
    <h3 class="font-medium text-gray-900 mb-4">Программа: {{ $application->program->specialty->full_title }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div><label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Приоритет</label><select id="priority" name="priority" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white">@for($i=1;$i<=5;$i++)<option value="{{ $i }}" {{ old('priority', $application->priority) == $i ? 'selected' : '' }}>{{ $i }}</option>@endfor</select></div>
      @if($application->program->has_study_form)<div><label for="study_form" class="block text-sm font-medium text-gray-700 mb-1">Форма обучения</label><select id="study_form" name="study_form" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white"><option value="full_time" {{ old('study_form', $application->study_form)==='full_time'?'selected':'' }}>Очная</option><option value="part_time" {{ old('study_form', $application->study_form)==='part_time'?'selected':'' }}>Заочная</option></select></div>@else<input type="hidden" name="study_form" value="full_time">@endif
      <div><label for="funding_type" class="block text-sm font-medium text-gray-700 mb-1">Финансирование</label><select id="funding_type" name="funding_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white"><option value="budget" {{ old('funding_type', $application->funding_type)==='budget'?'selected':'' }}>Бюджет</option><option value="paid" {{ old('funding_type', $application->funding_type)==='paid'?'selected':'' }}>Платное</option></select></div>
      <div><label for="doc_type" class="block text-sm font-medium text-gray-700 mb-1">Тип документа</label><select id="doc_type" name="doc_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white"><option value="original" {{ old('doc_type', $application->doc_type)==='original'?'selected':'' }}>Оригинал</option><option value="copy" {{ old('doc_type', $application->doc_type)==='copy'?'selected':'' }}>Копия</option></select></div>
    </div>
    <div class="mt-4 flex flex-wrap gap-4">
      <label class="flex items-center"><input type="hidden" name="is_benefit" value="0"><input type="checkbox" name="is_benefit" value="1" {{ old('is_benefit', $application->is_benefit) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded mr-2">Льгота</label>
      <label class="flex items-center"><input type="hidden" name="needs_dorm" value="0"><input type="checkbox" name="needs_dorm" value="1" {{ old('needs_dorm', $application->needs_dorm) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded mr-2">Общежитие</label>
      <label class="flex items-center"><input type="hidden" name="is_first_spo" value="0"><input type="checkbox" name="is_first_spo" value="1" {{ old('is_first_spo', $application->is_first_spo) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded mr-2">Первое СПО</label>
    </div>
  </div>

  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
    <h3 class="font-medium text-gray-900 mb-4">Оценки по предметам</h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      @foreach($application->scores as $i => $score)
      <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ $score->subject_name }}</label><input type="hidden" name="scores[{{ $i }}][subject_name]" value="{{ $score->subject_name }}"><input type="number" name="scores[{{ $i }}][score]" min="2" max="5" step="0.5" value="{{ old("scores.{$i}.score", $score->score) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" placeholder="2.0 — 5.0"></div>
      @endforeach
    </div>
  </div>

  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
    <h3 class="font-medium text-gray-900 mb-4">Подписанное заявление</h3>
    @if($application->signed_doc_photo)<img src="{{ Storage::url($application->signed_doc_photo) }}" alt="" class="mb-3 max-h-48 rounded-lg">@endif
    <input type="file" name="signed_doc_photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700">
  </div>

  <div class="flex justify-end gap-3">
    <a href="{{ route('applicant.applications.show', $application) }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">Отмена</a>
    <button type="submit" class="px-8 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">Сохранить и отправить</button>
  </div>
</form>
@endsection
