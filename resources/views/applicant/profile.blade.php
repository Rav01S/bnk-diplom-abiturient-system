@extends('layouts.app')
@section('title', 'Профиль | Приёмная комиссия БНК')
@section('header', 'Мой профиль')
@section('sidebar') @include('partials.sidebar-applicant') @endsection
@section('styles') .photo-preview { min-height: 120px; } @endsection
@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
  <div><h2 class="text-xl font-semibold text-gray-900">Личные данные</h2><p class="text-gray-500 text-sm">Заполните профиль для автоматического заполнения заявлений</p></div>
  <a href="{{ route('password.edit') }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm transition hover:shadow-md">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
    Сменить пароль
  </a>
</div>

<form method="POST" action="{{ route('applicant.profile.update') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
  @csrf @method('PUT')

  {{-- Основная информация --}}
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
    <h3 class="font-medium text-gray-900 mb-4 pb-3 border-b border-gray-200">Основная информация</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div><label for="last_name" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Фамилия</label><input type="text" id="last_name" name="last_name" required value="{{ old('last_name', $applicant->last_name) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="first_name" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Имя</label><input type="text" id="first_name" name="first_name" required value="{{ old('first_name', $applicant->first_name) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Отчество</label><input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $applicant->middle_name) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Дата рождения</label><input type="date" id="birth_date" name="birth_date" required value="{{ old('birth_date', $applicant->birth_date?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="phone" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Телефон</label><input type="tel" id="phone" name="phone" required value="{{ old('phone', $applicant->phone) }}" placeholder="+7 (999) 123-45-67" inputmode="tel" autocomplete="tel" maxlength="18" pattern="^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$" data-mask="phone" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="snils" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">СНИЛС</label><input type="text" id="snils" name="snils" required value="{{ old('snils', $applicant->snils) }}" placeholder="123-456-789 01" inputmode="numeric" maxlength="14" pattern="^\d{3}-\d{3}-\d{3} \d{2}$" data-mask="snils" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
    </div>
  </div>

  {{-- Паспортные данные --}}
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
    <h3 class="font-medium text-gray-900 mb-4 pb-3 border-b border-gray-200">Паспортные данные</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div><label for="passport_series" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Серия</label><input type="text" id="passport_series" name="passport_series" required maxlength="4" placeholder="1234" inputmode="numeric" pattern="\d{4}" data-mask="passport-series" value="{{ old('passport_series', $applicant->passport_series) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="passport_number" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Номер</label><input type="text" id="passport_number" name="passport_number" required maxlength="6" placeholder="567890" inputmode="numeric" pattern="\d{6}" data-mask="passport-number" value="{{ old('passport_number', $applicant->passport_number) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div class="sm:col-span-2 lg:col-span-3"><label for="passport_issued_by" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Кем выдан</label><input type="text" id="passport_issued_by" name="passport_issued_by" required value="{{ old('passport_issued_by', $applicant->passport_issued_by) }}" placeholder="ОУФМС России по г. Москве, отдел №1" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
    </div>
    <div class="mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Фото разворота паспорта <span class="text-danger">*</span></label>
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1"><input type="file" name="photo_passport" accept="image/*" data-preview="passportPreview" {{ $applicant->photo_passport ? '' : 'required' }} class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"><p class="mt-1 text-xs text-gray-500">JPEG, PNG до 5 МБ</p></div>
        <div id="passportPreview" class="photo-preview flex items-center justify-center w-full sm:w-40 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
          @if($applicant->photo_passport)
          <img src="{{ Storage::url($applicant->photo_passport) }}" alt="Паспорт" class="max-h-32 rounded-lg shadow-sm object-cover">
          @else
          <span class="text-xs text-gray-400 text-center px-2">Предпросмотр</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Образование --}}
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
    <h3 class="font-medium text-gray-900 mb-4 pb-3 border-b border-gray-200">Документ об образовании</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div><label for="prev_education" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Уровень образования</label><select id="prev_education" name="prev_education" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring bg-white"><option value="">Выберите...</option><option value="9class" {{ old('prev_education', $applicant->prev_education)==='9class'?'selected':'' }}>Основное общее образование</option><option value="11class" {{ old('prev_education', $applicant->prev_education)==='11class'?'selected':'' }}>Среднее общее образование</option><option value="spo" {{ old('prev_education', $applicant->prev_education)==='spo'?'selected':'' }}>СПО (колледж/техникум)</option><option value="vo" {{ old('prev_education', $applicant->prev_education)==='vo'?'selected':'' }}>ВО (неоконченное высшее)</option></select></div>
      <div><label for="avg_cert_score" class="block text-sm font-medium text-gray-700 mb-1" aria-required="true">Средний балл аттестата</label><input type="text" id="avg_cert_score" name="avg_cert_score" required inputmode="decimal" placeholder="4.75" value="{{ old('avg_cert_score', $applicant->avg_cert_score) }}" data-mask="avg-score" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="edu_doc_series" class="block text-sm font-medium text-gray-700 mb-1">Серия документа</label><input type="text" id="edu_doc_series" name="edu_doc_series" value="{{ old('edu_doc_series', $applicant->edu_doc_series) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="edu_doc_number" class="block text-sm font-medium text-gray-700 mb-1">Номер документа</label><input type="text" id="edu_doc_number" name="edu_doc_number" value="{{ old('edu_doc_number', $applicant->edu_doc_number) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div class="sm:col-span-2"><label for="edu_doc_issued_by" class="block text-sm font-medium text-gray-700 mb-1">Кем выдан</label><input type="text" id="edu_doc_issued_by" name="edu_doc_issued_by" value="{{ old('edu_doc_issued_by', $applicant->edu_doc_issued_by) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
      <div><label for="edu_issue_date" class="block text-sm font-medium text-gray-700 mb-1">Дата выдачи</label><input type="date" id="edu_issue_date" name="edu_issue_date" value="{{ old('edu_issue_date', $applicant->edu_issue_date?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition focus-ring"></div>
    </div>
    <div class="mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Фото документа об образовании</label>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach(['photo_edu_1'=>'Стр. 1','photo_edu_2'=>'Стр. 2','photo_edu_3'=>'Приложение'] as $field=>$label)
        <div>
          <input type="file" name="{{ $field }}" accept="image/*" data-preview="{{ $field }}_preview" class="block w-full text-sm text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
          <div id="{{ $field }}_preview" class="photo-preview mt-2 flex items-center justify-center bg-gray-50 rounded border-2 border-dashed border-gray-300">
            @if($applicant->$field)
            <img src="{{ Storage::url($applicant->$field) }}" alt="{{ $label }}" class="max-h-32 rounded-lg shadow-sm object-cover">
            @else
            <span class="text-xs text-gray-400">{{ $label }}</span>
            @endif
          </div>
        </div>
        @endforeach
      </div>
      <p class="mt-2 text-xs text-gray-500">Загрузите фото обложки, разворота с данными и приложения с оценками</p>
    </div>
  </div>

  {{-- СНИЛС --}}
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
    <h3 class="font-medium text-gray-900 mb-4 pb-3 border-b border-gray-200">Дополнительно</h3>
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="flex-1"><label for="photo_snils" class="block text-sm font-medium text-gray-700 mb-2">Фото СНИЛС <span class="text-danger">*</span></label><input type="file" id="photo_snils" name="photo_snils" accept="image/*" data-preview="snilsPreview" {{ $applicant->photo_snils ? '' : 'required' }} class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"></div>
      <div id="snilsPreview" class="photo-preview flex items-center justify-center w-full sm:w-40 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
        @if($applicant->photo_snils)
        <img src="{{ Storage::url($applicant->photo_snils) }}" alt="СНИЛС" class="max-h-32 rounded-lg shadow-sm object-cover">
        @else
        <span class="text-xs text-gray-400">Предпросмотр</span>
        @endif
      </div>
    </div>
  </div>

  <div class="flex justify-end">
    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition focus:ring-4 focus:ring-primary-100 focus-ring">
      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Сохранить изменения
    </button>
  </div>
</form>
@endsection
@section('scripts')
<script>
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
  let totalSize = 0;
  this.querySelectorAll('input[type="file"]').forEach(input => {
    if (input.files?.[0]) totalSize += input.files[0].size;
  });
  
  // 8MB limit (approximate, PHP limit is exactly 8388608 bytes)
  if (totalSize > 8 * 1024 * 1024) {
    e.preventDefault();
    alert('Суммарный размер файлов превышает 8 МБ. Пожалуйста, загрузите файлы меньшего размера или загружайте их по отдельности.');
  }
});

document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
  input.addEventListener('change', function(e) {
    const previewId = this.dataset.preview;
    const preview = document.getElementById(previewId);
    if (!preview || !e.target.files?.[0]) return;
    const file = e.target.files[0];
    if (!file.type.startsWith('image/')) { alert('Выберите изображение'); return; }
    const reader = new FileReader();
    reader.onload = function(ev) { preview.innerHTML = '<img src="' + ev.target.result + '" alt="Предпросмотр" class="max-h-32 rounded-lg shadow-sm object-cover">'; };
    reader.readAsDataURL(file);
  });
});

function digitsOnly(value) {
  return value.replace(/\D/g, '');
}

document.querySelector('[data-mask="phone"]')?.addEventListener('input', function() {
  let digits = digitsOnly(this.value);
  if (digits.startsWith('8')) digits = '7' + digits.slice(1);
  if (!digits.startsWith('7')) digits = '7' + digits;
  digits = digits.slice(0, 11);
  const body = digits.slice(1);
  let result = '+7';
  if (body.length > 0) result += ' (' + body.slice(0, 3);
  if (body.length >= 3) result += ')';
  if (body.length > 3) result += ' ' + body.slice(3, 6);
  if (body.length > 6) result += '-' + body.slice(6, 8);
  if (body.length > 8) result += '-' + body.slice(8, 10);
  this.value = result;
});

document.querySelector('[data-mask="snils"]')?.addEventListener('input', function() {
  const digits = digitsOnly(this.value).slice(0, 11);
  const parts = [digits.slice(0, 3), digits.slice(3, 6), digits.slice(6, 9)].filter(Boolean);
  this.value = parts.join('-') + (digits.length > 9 ? ' ' + digits.slice(9, 11) : '');
});

document.querySelector('[data-mask="passport-series"]')?.addEventListener('input', function() {
  this.value = digitsOnly(this.value).slice(0, 4);
});

document.querySelector('[data-mask="passport-number"]')?.addEventListener('input', function() {
  this.value = digitsOnly(this.value).slice(0, 6);
});

document.querySelector('[data-mask="avg-score"]')?.addEventListener('input', function() {
  let value = this.value.replace(',', '.').replace(/[^\d.]/g, '');
  const firstDot = value.indexOf('.');
  if (firstDot !== -1) {
    value = value.slice(0, firstDot + 1) + value.slice(firstDot + 1).replace(/\./g, '');
  }
  if (value.length > 4) value = value.slice(0, 4);
  const numeric = parseFloat(value);
  if (!Number.isNaN(numeric) && numeric > 5) value = '5';
  this.value = value;
});
</script>
@endsection
