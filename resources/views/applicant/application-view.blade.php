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
      <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-900"><strong>Комментарий сотрудника:</strong> {{ $application->rejection_reason }}</div>
      @endif
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><p class="text-gray-500 text-xs">ФИО</p><p class="font-medium">{{ $application->app_full_name }}</p></div>
        <div><p class="text-gray-500 text-xs">Дата рождения</p><p class="font-medium">{{ $application->app_birth_date?->format('d.m.Y') ?? '—' }}</p></div>
        <div><p class="text-gray-500 text-xs">Паспорт</p><p class="font-mono">{{ $application->app_passport_series }} {{ $application->app_passport_number }}</p></div>
        <div><p class="text-gray-500 text-xs">СНИЛС</p><p class="font-mono">{{ $application->app_snils }}</p></div>
        <div><p class="text-gray-500 text-xs">Специальность</p><p class="font-medium">{{ $application->program->specialty->full_title }}</p></div>
        <div><p class="text-gray-500 text-xs">Форма / Финансирование</p><p class="font-medium">{{ $application->study_form === 'full_time' ? 'Очная' : 'Заочная' }} / {{ $application->funding_type === 'budget' ? 'Бюджет' : 'Хозрасчёт' }}</p></div>
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
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-medium text-gray-900">Позиция в рейтинге</h3>
        <span class="text-xs text-gray-500">{{ $application->funding_type === 'budget' ? 'Бюджет' : 'Хозрасчёт' }} • Мест: {{ $application->funding_type === 'budget' ? $application->program->plan_count : $application->program->plan_count_paid }}</span>
      </div>
      
      <div class="overflow-x-auto -mx-4 sm:mx-0">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-bold">
            <tr>
              <th class="px-4 py-2 text-left">№</th>
              <th class="px-4 py-2 text-left">ФИО</th>
              <th class="px-4 py-2 text-center">Ср. балл аттестата</th>
              <th class="px-4 py-2 text-center">Балл по трем предметам</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @foreach($ranking as $index => $app)
              <tr class="{{ $app->id === $application->id ? 'bg-primary-50 ring-1 ring-inset ring-primary-500' : '' }} hover:bg-gray-50">
                <td class="px-4 py-2.5 font-bold {{ $app->id === $application->id ? 'text-primary-700' : 'text-gray-500' }}">
                  {{ $index + 1 }}
                </td>
                <td class="px-4 py-2.5">
                  <div class="flex items-center">
                    <span class="{{ $app->id === $application->id ? 'font-bold text-primary-900' : 'text-gray-900' }}">
                      {{ $app->app_full_name }}
                    </span>
                    @if($app->is_benefit && $app->benefit_type)
                      <span class="ml-1 text-[8px] font-bold px-1 py-0.5 bg-amber-100 text-amber-700 rounded uppercase">
                        {{ $app->benefit_type === 'svo' ? 'СВО' : ($app->benefit_type === 'olympiad' ? 'Олимп.' : 'Льгота') }}
                      </span>
                    @endif
                  </div>
                </td>
                <td class="px-4 py-2.5 text-center {{ $app->id === $application->id ? 'font-bold text-primary-700' : '' }}">
                  @if($app->is_benefit && $app->benefit_type === 'svo')
                    <span class="inline-flex items-center text-primary-700 font-bold">
                      <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                      6.00 ({{ number_format($app->applicant->avg_cert_score ?? 0, 2, ',', '') }})
                    </span>
                  @else
                    {{ number_format($app->applicant->avg_cert_score ?? 0, 2, ',', '') }}
                  @endif
                </td>
                <td class="px-4 py-2.5 text-center {{ $app->id === $application->id ? 'font-bold text-primary-700' : '' }}">
                  {{ number_format($app->average_score, 2, ',', '') }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @php 
        $myRank = $ranking->search(fn($item) => $item->id === $application->id) + 1;
        $plan = $application->funding_type === 'budget' ? $application->program->plan_count : $application->program->plan_count_paid;
      @endphp
      <div class="mt-4 p-3 rounded-lg {{ $myRank <= $plan ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }} text-center text-sm font-medium">
        @if($myRank <= $plan)
          ✓ Вы проходите на текущий момент (позиция {{ $myRank }} из {{ $plan }})
        @else
          ⚠ Вы пока не проходите в основной список (позиция {{ $myRank }} из {{ $plan }})
        @endif
      </div>
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
