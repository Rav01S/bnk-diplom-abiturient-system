@extends('layouts.app')
@section('title', 'Подать заявление | Приёмная комиссия БНК')
@section('header', 'Новое заявление')
@section('sidebar') @include('partials.sidebar-applicant') @endsection
@section('styles')
.wizard-step { display: none; }
.wizard-step.active { display: block; }
.step-dot { transition: all 0.2s; }
.step-dot.active { background-color: #0b5f56; border-color: #0b5f56; color: white; }
.step-dot.completed { background-color: #059669; border-color: #059669; color: white; }
.print-preview { font-family: 'Times New Roman', serif; font-size: 14px; line-height: 1.5; }
.photo-preview { min-height: 120px; }
.choice-option.selected { border-color: #0b5f56; background-color: #edf7f4; }
.choice-option.disabled { cursor: not-allowed; opacity: 0.55; background-color: #f8fafc; }
@endsection
@section('content')
@if($limitReached)
<div class="bg-white rounded-lg shadow-sm border border-orange-200 p-8 text-center">
  <svg class="mx-auto w-16 h-16 text-orange-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
  <h2 class="text-xl font-semibold text-gray-900 mb-2">Лимит достигнут</h2>
  <p class="text-gray-600">Вы подали максимальное количество заявлений (5). Отмените одно из текущих, чтобы подать новое.</p>
</div>
@elseif($profileIncomplete ?? false)
<div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-8 text-center">
  <svg class="mx-auto mb-4 h-16 w-16 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
  <h2 class="mb-2 text-xl font-semibold text-gray-900">Заполните профиль</h2>
  <p class="mx-auto mb-6 max-w-xl text-gray-600">Подача заявления доступна только после заполнения обязательных данных профиля: телефон, СНИЛС, паспортные данные, образование, средний балл аттестата, фото паспорта и фото СНИЛС.</p>
  <a href="{{ route('applicant.profile') }}" class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-primary-700">Перейти к профилю</a>
</div>
@else
@php
  $occupiedPriorities = collect($occupiedPriorities ?? []);
  $maxSelectablePriority = min((int) ($maxSelectablePriority ?? 1), 5);
  $defaultPriority = collect(range(1, $maxSelectablePriority))->first(fn ($priority) => ! $occupiedPriorities->contains($priority)) ?? $maxSelectablePriority;
  $selectedPriority = (int) old('priority', $defaultPriority);
@endphp

{{-- Wizard Progress --}}
<div class="mb-6">
  <div class="wizard-progress flex items-center justify-between max-w-3xl mx-auto">
    <div class="flex flex-col items-center flex-1"><div class="step-dot active w-8 h-8 rounded-full border-2 border-primary-600 bg-white flex items-center justify-center text-sm font-medium text-primary-600 mb-2" data-step="1">1</div><span class="text-xs text-gray-600 text-center">Программа</span></div>
    <div class="flex-1 h-1 bg-gray-200 mx-2 step-line"></div>
    <div class="flex flex-col items-center flex-1"><div class="step-dot w-8 h-8 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center text-sm font-medium text-gray-400 mb-2" data-step="2">2</div><span class="text-xs text-gray-600 text-center">Данные</span></div>
    <div class="flex-1 h-1 bg-gray-200 mx-2 step-line"></div>
    <div class="flex flex-col items-center flex-1"><div class="step-dot w-8 h-8 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center text-sm font-medium text-gray-400 mb-2" data-step="3">3</div><span class="text-xs text-gray-600 text-center">Предпросмотр</span></div>
    <div class="flex-1 h-1 bg-gray-200 mx-2 step-line"></div>
    <div class="flex flex-col items-center flex-1"><div class="step-dot w-8 h-8 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center text-sm font-medium text-gray-400 mb-2" data-step="4">4</div><span class="text-xs text-gray-600 text-center">Отправка</span></div>
  </div>
</div>

<form id="wizardForm" method="POST" action="{{ route('applicant.applications.store') }}" enctype="multipart/form-data" class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200" novalidate>
  @csrf

  {{-- ШАГ 1: Выбор программы --}}
  <div class="wizard-step active p-4 sm:p-6" id="step1">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Шаг 1: Выбор образовательной программы</h3>
    <div class="space-y-4">
      <div><label for="program_id" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Специальность</label>
        <select id="program_id" name="program_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring bg-white">
          <option value="">Выберите специальность...</option>
          @foreach($programs as $prog)<option value="{{ $prog->id }}" data-accepting="1" data-has-form="{{ $prog->has_study_form ? '1' : '0' }}" data-plan-budget="{{ $prog->plan_count }}" data-plan-paid="{{ $prog->plan_count_paid }}" data-subjects='@json($prog->specialty->subjects)' @selected((int) old('program_id') === $prog->id)>{{ $prog->specialty->full_title }}{{ !$prog->has_study_form ? ' (только очная)' : '' }}</option>@endforeach
          @foreach(($specialtiesWithoutPrograms ?? collect()) as $specialty)<option value="" disabled>{{ $specialty->full_title }} — программа приёма не создана</option>@endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Код и название по ФГОС</p>
        <p id="programHint" class="mt-2 hidden text-xs text-amber-700"></p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2" aria-required="true">Форма обучения</label>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <label class="study-form-option choice-option flex cursor-pointer items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-3 transition hover:border-primary-600">
            <span>
              <span class="block text-sm font-medium text-gray-900">Очная</span>
              <span class="block text-xs text-gray-500">Обучение в колледже по расписанию</span>
            </span>
            <input type="radio" name="study_form" value="full_time" class="h-4 w-4 text-primary-600" {{ old('study_form', 'full_time') === 'full_time' ? 'checked' : '' }}>
          </label>
          <label id="partTimeOption" class="study-form-option choice-option flex cursor-pointer items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-3 transition hover:border-primary-600">
            <span>
              <span class="block text-sm font-medium text-gray-900">Заочная</span>
              <span class="block text-xs text-gray-500">Обучение с сессиями и самостоятельной работой</span>
            </span>
            <input id="partTimeInput" type="radio" name="study_form" value="part_time" class="h-4 w-4 text-primary-600" {{ old('study_form') === 'part_time' ? 'checked' : '' }}>
          </label>
        </div>
        <p id="studyFormHint" class="mt-2 hidden text-xs text-amber-700">Для выбранной программы доступна только очная форма обучения.</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2" aria-required="true">Финансирование</label>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <label id="budgetOption" class="funding-option choice-option flex cursor-pointer items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-3 transition hover:border-primary-600">
            <span>
              <span class="block text-sm font-medium text-gray-900">Бюджет</span>
              <span class="block text-xs text-gray-500">Обучение за счёт бюджетных мест</span>
            </span>
            <input id="budgetInput" type="radio" name="funding_type" value="budget" class="h-4 w-4 text-primary-600" {{ old('funding_type', 'budget') === 'budget' ? 'checked' : '' }}>
          </label>
          <label id="paidOption" class="funding-option choice-option flex cursor-pointer items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-3 transition hover:border-primary-600">
            <span>
              <span class="block text-sm font-medium text-gray-900">Хозрасчёт</span>
              <span class="block text-xs text-gray-500">Обучение по договору на хозрасчётной основе</span>
            </span>
            <input id="paidInput" type="radio" name="funding_type" value="paid" class="h-4 w-4 text-primary-600" {{ old('funding_type') === 'paid' ? 'checked' : '' }}>
          </label>
        </div>
        <p id="fundingHint" class="mt-2 hidden text-xs text-amber-700"></p>
      </div>
      <div><label for="priorityInput" class="block text-sm font-medium text-gray-700 mb-2" aria-required="true">Приоритет заявления (1–5)</label>
        <select id="priorityInput" name="priority" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring bg-white">
          @for($i = 1; $i <= $maxSelectablePriority; $i++)
            <option value="{{ $i }}" @selected($selectedPriority === $i)>{{ $i }}{{ $occupiedPriorities->contains($i) ? ' — занят, сдвинет следующие' : '' }}</option>
          @endfor
        </select>
        <p class="mt-1 text-xs text-gray-500">1 — высший приоритет. При новой подаче доступен максимум: количество активных заявлений + 1, но не выше 5. Если номер занят, он и следующие сдвинутся на +1.</p>
      </div>
    </div>
    <div class="mt-8 flex justify-end"><button type="button" id="step1Next" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>Далее →</button></div>
  </div>

  {{-- ШАГ 2: Данные (снапшот) --}}
  <div class="wizard-step p-4 sm:p-6" id="step2">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Шаг 2: Данные для заявления</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 p-4 bg-primary-50 rounded-lg border border-primary-100">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Фамилия</label><input type="text" value="{{ $applicant->last_name }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Имя</label><input type="text" value="{{ $applicant->first_name }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Отчество</label><input type="text" value="{{ $applicant->middle_name }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Дата рождения</label><input type="text" value="{{ $applicant->birth_date?->format('d.m.Y') }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Паспорт (серия/номер)</label><div class="flex space-x-2"><input type="text" value="{{ $applicant->passport_series }}" readonly class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50"><input type="text" value="{{ $applicant->passport_number }}" readonly class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50"></div></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">СНИЛС</label><input type="text" value="{{ $applicant->snils }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50"></div>
    </div>

    <h4 class="font-medium text-gray-900 mb-3">Оценки из аттестата (по 5-балльной шкале)</h4>
    <div id="subjectsContainer" class="space-y-3 mb-6"></div>
    <p class="text-xs text-gray-500 mb-6">Введите оценки из аттестата по требуемым предметам (2.0 – 5.0)</p>

    <div class="grid grid-cols-1 gap-8 mb-6 md:grid-cols-2">
      <div class="space-y-5">
        <div>
          <label class="block text-base font-semibold text-gray-900 mb-3" aria-required="true">Документ об образовании</label>
          <div class="space-y-4">
            <label class="flex items-center">
              <input type="radio" name="doc_type" value="original" {{ old('doc_type', 'original')==='original'?'checked':'' }} class="w-4 h-4 text-primary-600">
              <span class="ml-2 text-sm">Оригинал</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="doc_type" value="copy" {{ old('doc_type')==='copy'?'checked':'' }} class="w-4 h-4 text-primary-600">
              <span class="ml-2 text-sm">Заверенная копия</span>
            </label>
          </div>
        </div>
        <label class="flex items-center">
          <input type="hidden" name="needs_dorm" value="0">
          <input type="checkbox" name="needs_dorm" value="1" {{ old('needs_dorm')?'checked':'' }} class="w-4 h-4 text-primary-600 rounded">
          <span class="ml-2 text-sm">Нуждаюсь в общежитии</span>
        </label>
      </div>
      <div class="space-y-5">
        <div>
          <label class="block text-base font-semibold text-gray-900 mb-3">Особые права</label>
          <div class="space-y-3">
            <label class="flex items-center">
              <input type="hidden" name="is_benefit" value="0">
              <input type="checkbox" id="isBenefit" name="is_benefit" value="1" {{ old('is_benefit')?'checked':'' }} class="w-4 h-4 text-primary-600 rounded">
              <span class="ml-2 text-sm">Есть льготные права</span>
            </label>
            <select id="benefitType" name="benefit_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm {{ old('is_benefit') ? 'bg-white' : 'bg-gray-50 opacity-50' }}" {{ old('is_benefit') ? 'required' : 'disabled' }}>
              <option value="olympiad" {{ old('benefit_type')==='olympiad'?'selected':'' }}>Олимпиада</option>
              <option value="disability" {{ old('benefit_type')==='disability'?'selected':'' }}>Инвалидность</option>
              <option value="svo" {{ old('benefit_type')==='svo'?'selected':'' }}>СВО</option>
            </select>
          </div>
        </div>
        <label class="flex items-center pt-1">
          <input type="hidden" name="is_first_spo" value="0">
          <input type="checkbox" name="is_first_spo" value="1" {{ old('is_first_spo', 1)?'checked':'' }} class="w-4 h-4 text-primary-600 rounded">
          <span class="ml-2 text-sm">Получаю СПО впервые</span>
        </label>
      </div>
    </div>

    <div class="mt-8 flex justify-between"><button type="button" id="step2Prev" class="px-6 py-2.5 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 font-medium rounded-lg">← Назад</button><button type="button" id="step2Next" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Далее →</button></div>
  </div>

  {{-- ШАГ 3: Предпросмотр --}}
  <div class="wizard-step p-4 sm:p-6" id="step3">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Шаг 3: Предпросмотр заявления</h3>
    <p class="text-sm text-gray-600 mb-4">Проверьте данные. Нажмите «Скачать шаблон», подпишите и загрузите фото на следующем шаге.</p>
    <div id="printArea" class="print-preview bg-white border border-gray-300 rounded-lg p-6 sm:p-8 mb-6 max-h-96 overflow-y-auto">
      <div class="text-center mb-6"><p class="font-bold text-lg">ЗАЯВЛЕНИЕ</p></div>
      <div class="space-y-3 text-sm">
        <p><strong>От абитуриента:</strong><br><span id="previewFio">{{ $applicant->full_name }}</span><br>Паспорт: <span id="previewPassport">{{ $applicant->passport_series }} {{ $applicant->passport_number }}</span><br>СНИЛС: <span id="previewSnils">{{ $applicant->snils }}</span></p>
        <p><strong>Прошу зачислить:</strong><br><span id="previewProgram"></span></p>
        <p><strong>Оценки в аттестате:</strong><br><span id="previewScores"></span></p>
      </div>
    </div>
    <div class="flex flex-wrap gap-3 mb-6">
      <button type="button" id="downloadTemplateBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">📄 Скачать заполненный шаблон</button>
      <a href="{{ route('applicant.applications.empty-template') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 flex items-center">📄 Скачать пустой бланк</a>
    </div>
    <div class="mt-8 flex justify-between"><button type="button" id="step3Prev" class="px-6 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-lg">← Назад</button><button type="button" id="step3Next" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Далее →</button></div>
  </div>

  {{-- ШАГ 4: Загрузка + Отправка --}}
  <div class="wizard-step p-4 sm:p-6" id="step4">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Шаг 4: Загрузка подписанного заявления</h3>
    <p class="mb-6 text-sm text-gray-600">Загрузите чёткое фото или скан подписанного заявления. Формат: JPG, PNG, PDF до 10 МБ.</p>
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-900 mb-3" aria-required="true">Фото подписанного заявления</label>
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1"><input type="file" id="signedDocPhoto" name="signed_doc_photo" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" data-preview="signedDocPreview" required class="block w-full text-sm text-gray-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"></div>
        <div id="signedDocPreview" class="flex min-h-10 items-center text-sm text-gray-500">Файл не выбран</div>
      </div>
      <p class="mt-3 text-sm text-gray-500">Убедитесь, что подпись и дата видны чётко. Максимальный размер: 10 МБ.</p>
    </div>
    <div class="mb-6 rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-4"><label class="flex items-start"><input type="checkbox" id="confirmSubmit" required class="mt-1 w-4 h-4 text-primary-600 rounded"><span class="ml-3 text-sm leading-6">Я подтверждаю, что все данные в заявлении верны, документ подписан собственноручно, и даю согласие на обработку персональных данных.</span></label></div>
    <div class="mb-8 rounded-lg border border-gray-200 bg-gray-50 p-4">
      <h4 class="mb-3 font-semibold text-gray-900">Итого:</h4>
      <div class="space-y-2 text-sm">
        <p>Заявление №: <strong>будет присвоен после отправки</strong></p>
        <p>Программа: <strong id="summaryProgram">—</strong></p>
        <p>Приоритет: <strong id="summaryPriority">{{ $selectedPriority }}</strong></p>
        <p>Статус после отправки: <span class="inline-flex rounded-full bg-primary-100 px-2.5 py-1 text-xs font-medium text-primary-700">На проверке</span></p>
      </div>
    </div>
    <div class="mt-8 flex justify-between"><button type="button" id="step4Prev" class="px-6 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-lg">← Назад</button><button type="submit" id="submitBtn" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg disabled:opacity-50" disabled>Отправить заявление</button></div>
  </div>
</form>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const programSelect = document.getElementById('program_id');
  const priorityRange = document.getElementById('priorityInput');
  const priorityValue = document.getElementById('priorityValue');
  const subjectsContainer = document.getElementById('subjectsContainer');
  const step1Next = document.getElementById('step1Next');
  const programHint = document.getElementById('programHint');
  const benefitCheck = document.getElementById('isBenefit');
  const benefitType = document.getElementById('benefitType');
  const partTimeOption = document.getElementById('partTimeOption');
  const partTimeInput = document.getElementById('partTimeInput');
  const studyFormHint = document.getElementById('studyFormHint');
  const budgetOption = document.getElementById('budgetOption');
  const budgetInput = document.getElementById('budgetInput');
  const paidOption = document.getElementById('paidOption');
  const paidInput = document.getElementById('paidInput');
  const fundingHint = document.getElementById('fundingHint');
  let currentStep = 1;

  // Priority range
  priorityRange?.addEventListener('input', function() { if (priorityValue) priorityValue.textContent = this.value; });
  priorityRange?.addEventListener('change', function() { if (priorityValue) priorityValue.textContent = this.value; });

  function updateStudyFormState() {
    const selected = programSelect?.options[programSelect.selectedIndex];
    const allowsPartTime = selected?.dataset.hasForm !== '0';

    if (!allowsPartTime) {
      document.querySelector('input[name="study_form"][value="full_time"]').checked = true;
    }

    if (partTimeInput) {
      partTimeInput.disabled = !allowsPartTime;
    }

    partTimeOption?.classList.toggle('disabled', !allowsPartTime);
    studyFormHint?.classList.toggle('hidden', allowsPartTime);

    document.querySelectorAll('.study-form-option').forEach(label => {
      const input = label.querySelector('input[name="study_form"]');
      label.classList.toggle('selected', !!input?.checked);
    });
  }

  document.querySelectorAll('input[name="study_form"]').forEach(input => {
    input.addEventListener('change', updateStudyFormState);
  });

  function updateFundingState() {
    const selected = programSelect?.options[programSelect.selectedIndex];
    const hasBudget = (parseInt(selected?.dataset.planBudget || '0', 10) || 0) > 0;
    const hasPaid = (parseInt(selected?.dataset.planPaid || '0', 10) || 0) > 0;

    if (budgetInput) budgetInput.disabled = !hasBudget;
    if (paidInput) paidInput.disabled = !hasPaid;

    budgetOption?.classList.toggle('disabled', !hasBudget);
    paidOption?.classList.toggle('disabled', !hasPaid);

    if (budgetInput?.checked && !hasBudget && hasPaid) paidInput.checked = true;
    if (paidInput?.checked && !hasPaid && hasBudget) budgetInput.checked = true;

    const messages = [];
    if (selected?.value && !hasBudget) messages.push('бюджет недоступен');
    if (selected?.value && !hasPaid) messages.push('хозрасчёт недоступен');
    if (fundingHint) {
      fundingHint.textContent = messages.length ? 'Для выбранной программы ' + messages.join(', ') + '.' : '';
      fundingHint.classList.toggle('hidden', messages.length === 0);
    }

    document.querySelectorAll('.funding-option').forEach(label => {
      const input = label.querySelector('input[name="funding_type"]');
      label.classList.toggle('selected', !!input?.checked);
    });

    const accepting = selected?.dataset.accepting !== '0';
    if (programHint) {
      programHint.textContent = selected?.value && !accepting ? 'По выбранной программе приём сейчас закрыт.' : '';
      programHint.classList.toggle('hidden', !selected?.value || accepting);
    }

    if (programSelect && step1Next) {
      step1Next.disabled = !programSelect.value || !accepting || (!hasBudget && !hasPaid);
    }
  }

  document.querySelectorAll('input[name="funding_type"]').forEach(input => {
    input.addEventListener('change', updateFundingState);
  });

  // Program select → enable next, populate subjects
  programSelect?.addEventListener('change', function() {
    step1Next.disabled = !this.value;
    updateStudyFormState();
    updateFundingState();
    if (this.value) {
      const opt = this.options[this.selectedIndex];
      try {
        const subjects = JSON.parse(opt.dataset.subjects || '[]');
        subjectsContainer.innerHTML = subjects.map((subj, i) =>
          '<div class="flex items-center space-x-3">' +
          '<span class="w-32 text-sm text-gray-700">' + subj + '</span>' +
          '<input type="hidden" name="scores[' + i + '][subject_name]" value="' + subj + '">' +
          '<input type="number" name="scores[' + i + '][score]" min="2" max="5" step="0.5" placeholder="2-5" required class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-600">' +
          '<span class="text-xs text-gray-400">балл</span></div>'
        ).join('');
      } catch(e) {}
    }
  });

  // Benefit toggle
  benefitCheck?.addEventListener('change', function() {
    benefitType.disabled = !this.checked;
    benefitType.required = this.checked;
    benefitType.classList.toggle('opacity-50', !this.checked);
    benefitType.classList.toggle('bg-gray-50', !this.checked);
    if (this.checked && !benefitType.value) benefitType.value = benefitType.options[0]?.value || '';
    if (!this.checked) benefitType.value = '';
  });

  // Confirm checkbox
  function updateSubmitState() {
    const confirmed = document.getElementById('confirmSubmit')?.checked;
    const hasFile = document.getElementById('signedDocPhoto')?.files?.length > 0;
    document.getElementById('submitBtn').disabled = !(confirmed && hasFile);
  }

  document.getElementById('confirmSubmit')?.addEventListener('change', updateSubmitState);

  // File preview
  document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
    input.addEventListener('change', function(e) {
      const preview = document.getElementById(this.dataset.preview);
      if (!preview || !e.target.files?.[0]) {
        updateSubmitState();
        return;
      }
      const file = e.target.files[0];
      updateSubmitState();
      if (!file.type.startsWith('image/')) {
        preview.innerHTML = '<span class="inline-flex rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700">' + file.name + '</span>';
        return;
      }
      const reader = new FileReader();
      reader.onload = ev => { preview.innerHTML = '<img src="' + ev.target.result + '" class="max-h-32 rounded-lg shadow-sm object-cover">'; };
      reader.readAsDataURL(file);
    });
  });

  // Wizard navigation
  function goToStep(step) {
    if (step > currentStep && currentStep === 2) {
      let allValid = true;
      document.querySelectorAll('#subjectsContainer input[type="number"]').forEach(inp => {
        const v = parseFloat(inp.value);
        if (isNaN(v) || v < 2 || v > 5) { allValid = false; inp.classList.add('border-red-500'); }
        else { inp.classList.remove('border-red-500'); }
      });
      if (!allValid) { alert('Все оценки должны быть в диапазоне от 2 до 5'); return; }
    }
    currentStep = step;
    document.querySelectorAll('.wizard-step').forEach(el => el.classList.remove('active'));
    document.getElementById('step' + step)?.classList.add('active');
    // Update progress dots
    document.querySelectorAll('.step-dot').forEach(dot => {
      const n = parseInt(dot.dataset.step);
      dot.classList.remove('active', 'completed');
      if (n < step) { dot.classList.add('completed'); dot.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'; }
      else if (n === step) { dot.classList.add('active'); dot.textContent = n; }
      else { dot.textContent = n; }
    });
    // Preview step
    if (step === 3) {
      const opt = programSelect.options[programSelect.selectedIndex];
      document.getElementById('previewProgram').textContent = opt ? opt.textContent : '';
      let scoreHtml = '';
      document.querySelectorAll('#subjectsContainer > div').forEach(div => {
        const name = div.querySelector('input[type="hidden"]')?.value || '';
        const val = div.querySelector('input[type="number"]')?.value || '—';
        scoreHtml += '• ' + name + ': ' + val + '<br>';
      });
      document.getElementById('previewScores').innerHTML = scoreHtml;
    }
    if (step === 4) {
      const opt = programSelect.options[programSelect.selectedIndex];
      document.getElementById('summaryProgram').textContent = opt ? opt.textContent.trim() : '—';
      document.getElementById('summaryPriority').textContent = priorityRange?.value || '1';
      updateSubmitState();
    }
  }

  step1Next?.addEventListener('click', () => goToStep(2));
  document.getElementById('step2Prev')?.addEventListener('click', () => goToStep(1));
  document.getElementById('step2Next')?.addEventListener('click', () => goToStep(2 + 1));
  document.getElementById('step3Prev')?.addEventListener('click', () => goToStep(2));
  document.getElementById('step3Next')?.addEventListener('click', () => goToStep(4));
  document.getElementById('step4Prev')?.addEventListener('click', () => goToStep(3));

  // Download template
  document.getElementById('downloadTemplateBtn')?.addEventListener('click', function() {
    const form = document.getElementById('wizardForm');
    const oldAction = form.action;
    const oldTarget = form.target;
    
    // Set to new route
    form.action = "{{ route('applicant.applications.draft-template') }}";
    form.target = "_blank";
    
    // Temporarily remove required from signed_doc_photo because it's not needed for template download
    const photoInput = document.getElementById('signedDocPhoto');
    const oldRequired = photoInput ? photoInput.required : false;
    if (photoInput) photoInput.required = false;

    // Remove required from confirm checkbox
    const confirmInput = document.getElementById('confirmSubmit');
    let confirmRequired = false;
    if (confirmInput) {
       confirmRequired = confirmInput.required;
       confirmInput.required = false;
    }
    
    form.submit();
    
    // Revert
    setTimeout(() => {
        form.action = oldAction;
        form.target = oldTarget;
        if (photoInput) photoInput.required = oldRequired;
        if (confirmInput) confirmInput.required = confirmRequired;
    }, 100);
  });

  // Trigger initial state
  if (programSelect?.value) programSelect.dispatchEvent(new Event('change'));
  updateStudyFormState();
  updateFundingState();
});
</script>
@endsection
