@extends('layouts.app')
@section('title', 'Просмотр заявления | Приёмная комиссия БНК')
@section('header', 'Заявление №' . $application->id)
@section('sidebar') @include('partials.sidebar-applicant') @endsection
@section('content')
<div class="mb-4"><a href="{{ route('applicant.applications') }}" class="text-sm text-gray-600 hover:text-gray-900">← Назад к списку</a></div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-medium text-gray-900">Данные заявления</h3>
        @include('partials.status-badge', ['status' => $application->status])
      </div>
      @if($application->rejection_reason)
      <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800"><strong>Причина отклонения:</strong> {{ $application->rejection_reason }}</div>
      @endif
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><p class="text-gray-500 text-xs">ФИО</p><p class="font-medium">{{ $application->app_full_name }}</p></div>
        <div><p class="text-gray-500 text-xs">Дата рождения</p><p class="font-medium">{{ $application->app_birth_date?->format('d.m.Y') ?? '—' }}</p></div>
        <div><p class="text-gray-500 text-xs">Паспорт</p><p class="font-mono">{{ $application->app_passport_series }} {{ $application->app_passport_number }}</p></div>
        <div><p class="text-gray-500 text-xs">СНИЛС</p><p class="font-mono">{{ $application->app_snils }}</p></div>
        <div><p class="text-gray-500 text-xs">Специальность</p><p class="font-medium">{{ $application->program->specialty->full_title }}</p></div>
        <div><p class="text-gray-500 text-xs">Форма / Финансирование</p><p class="font-medium">{{ $application->study_form === 'full_time' ? 'Очная' : 'Заочная' }} / {{ $application->funding_type === 'budget' ? 'Бюджет' : 'Платно' }}</p></div>
        <div><p class="text-gray-500 text-xs">Приоритет</p><p class="font-medium">{{ $application->priority }}</p></div>
        <div><p class="text-gray-500 text-xs">Льгота</p><p class="font-medium">
          @if($application->is_benefit)
            {{ $application->benefit_type === 'olympiad' ? 'Олимпиада' : ($application->benefit_type === 'disability' ? 'Инвалидность' : ($application->benefit_type === 'svo' ? 'СВО' : $application->benefit_type)) }}
          @else
            Нет
          @endif
        </p></div>
        <div><p class="text-gray-500 text-xs">Общежитие</p><p class="font-medium">{{ $application->needs_dorm ? 'Нуждаюсь' : 'Не нуждаюсь' }}</p></div>
        <div><p class="text-gray-500 text-xs">Ревизия</p><p class="font-medium">{{ $application->revision }}</p></div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
      <h3 class="font-medium text-gray-900 mb-3">Оценки в аттестате</h3>
      <div class="grid grid-cols-3 gap-3">
        @foreach($application->scores as $score)
        <div class="p-3 bg-gray-50 rounded-lg text-center"><p class="text-xs text-gray-500">{{ $score->subject_name }}</p><p class="text-xl font-bold text-gray-900">{{ $score->score }}</p></div>
        @endforeach
      </div>
      <p class="mt-2 text-sm text-gray-600">Сумма баллов: <strong>{{ $application->total_score }}</strong></p>
    </div>
  </div>

  <div class="space-y-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
      <h3 class="font-medium text-gray-900 mb-3">Действия</h3>
      <div class="space-y-2">
        <a href="{{ route('applicant.applications.template', $application) }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">📄 Скачать шаблон</a>
        @if($application->isEditable())<a href="{{ route('applicant.applications.edit', $application) }}" class="block w-full text-center px-4 py-2 bg-warning-600 hover:bg-warning-700 text-white rounded-lg text-sm">✏️ Редактировать</a>@endif
        @if($application->isCancellable())<form method="POST" action="{{ route('applicant.applications.cancel', $application) }}" onsubmit="return confirm('Отменить заявление?')">@csrf @method('PATCH')<button type="submit" class="w-full px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white rounded-lg text-sm">✕ Отменить</button></form>@endif
      </div>
    </div>
    @if($application->signed_doc_photo)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
      <h3 class="font-medium text-gray-900 mb-3">Подписанный документ</h3>
      <img src="{{ Storage::url($application->signed_doc_photo) }}" alt="Подписанное заявление" class="w-full rounded-lg">
    </div>
    @endif
  </div>
</div>
@endsection
