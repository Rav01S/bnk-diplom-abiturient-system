@extends('layouts.app')
@section('title', 'Проверка заявления | Комиссия')
@section('header', 'Проверка заявления №' . $application->id)
@section('sidebar') @include('partials.sidebar-commission') @endsection
@section('styles') .zoom-overlay{position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:100;display:none;align-items:center;justify-content:center}.zoom-overlay.active{display:flex}.zoom-overlay img{max-height:90vh;max-width:90vw;border-radius:8px;} @endsection
@section('content')
<div class="mb-4"><a href="{{ route('commission.queue', request()->query()) }}" class="text-sm text-gray-600 hover:text-gray-900">← К очереди</a></div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
  {{-- Левая панель: снапшот + решение --}}
  <div class="lg:col-span-7 bg-white rounded-lg shadow-sm border border-gray-200 overflow-y-auto p-4 sm:p-5">
    <h2 class="text-lg font-medium text-gray-900 mb-4">Снапшот заявления</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4 p-4 bg-gray-50 rounded border border-gray-200">
      <div><p class="text-gray-500 text-xs">ФИО</p><p class="font-medium">{{ $application->app_full_name }}</p></div>
      <div><p class="text-gray-500 text-xs">Дата рождения</p><p class="font-medium">{{ $application->app_birth_date?->format('d.m.Y') ?? '—' }}</p></div>
      <div><p class="text-gray-500 text-xs">Паспорт</p><p class="font-mono">{{ $application->app_passport_series }} {{ $application->app_passport_number }}</p></div>
      <div><p class="text-gray-500 text-xs">СНИЛС</p><p class="font-mono">{{ $application->app_snils }}</p></div>
      <div><p class="text-gray-500 text-xs">Финансирование</p><p class="font-medium">{{ $application->funding_type === 'budget' ? 'Бюджет' : 'Хозрасчёт' }}</p></div>
      <div class="sm:col-span-2 p-2 bg-blue-50 rounded border border-blue-100 flex items-center justify-between">
        <div><p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Особые права (Льгота)</p>
        <p class="font-bold text-gray-900">
          @if($application->is_benefit)
            {{ $application->benefit_type === 'olympiad' ? 'Олимпиада' : ($application->benefit_type === 'disability' ? 'Инвалидность' : ($application->benefit_type === 'svo' ? 'СВО' : $application->benefit_type)) }}
          @else
            Нет
          @endif
        </p></div>
        @if($application->is_benefit)
          <span class="px-2 py-1 bg-blue-200 text-blue-800 text-[10px] font-bold rounded uppercase">Требует проверки</span>
        @endif
      </div>
    </div>

    <h3 class="font-medium text-gray-900 mb-2">Оценки в аттестате (предметы)</h3>
    <div class="grid grid-cols-3 gap-3 mb-6">
      @foreach($application->scores as $score)
      <div class="p-2 bg-gray-50 rounded text-center"><p class="text-xs text-gray-500">{{ $score->subject_name }}</p><p class="font-bold text-lg">{{ $score->score }} <span class="text-xs text-gray-400 font-normal">(балл)</span></p></div>
      @endforeach
    </div>
    <p class="-mt-4 mb-6 text-sm text-gray-600">Средний балл: <strong>{{ number_format($application->average_score, 2, ',', '') }}</strong></p>

    <form method="POST" action="{{ route('commission.review.submit', array_merge(['application' => $application], request()->query())) }}" id="reviewForm">
      @csrf
      <h3 class="font-medium text-gray-900 mb-2">Чек-лист сверки</h3>
      <div class="space-y-2 mb-6">
        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer"><input type="checkbox" class="review-check w-4 h-4 text-primary-600 rounded"><span class="text-sm text-gray-700">Паспортные данные совпадают с фото</span></label>
        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer"><input type="checkbox" class="review-check w-4 h-4 text-primary-600 rounded"><span class="text-sm text-gray-700">Фото аттестата читаемо</span></label>
        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer"><input type="checkbox" class="review-check w-4 h-4 text-primary-600 rounded"><span class="text-sm text-gray-700">Подпись на заявлении есть</span></label>
      </div>

      <h3 class="font-medium text-gray-900 mb-2">Комментарий сотрудника (обязателен при отклонении и доработке)</h3>
      <textarea id="rejection_reason" name="rejection_reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-4" rows="2" placeholder="Причина отклонения или список правок...">{{ old('rejection_reason', $application->rejection_reason) }}</textarea>

      <div class="flex flex-wrap gap-3 pt-2">
        <button type="submit" name="decision" value="approved" id="approveBtn" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg disabled:opacity-50" disabled>✓ Подтвердить</button>
        <button type="submit" name="decision" value="rework_needed" class="flex-1 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg">↻ На доработку</button>
        <button type="submit" name="decision" value="rejected" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">✕ Отклонить</button>
      </div>
    </form>
  </div>

  {{-- Правая панель: Галерея документов --}}
  <div class="lg:col-span-5 bg-white rounded-lg shadow-sm border border-gray-200 overflow-y-auto p-4">
    <h2 class="text-lg font-medium text-gray-900 mb-3">Галерея документов</h2>
    <div class="grid grid-cols-2 gap-3 mb-4">
      {{-- Паспорт --}}
      @if($application->app_photo_passport)
      <div class="cursor-pointer group relative rounded overflow-hidden border hover:border-primary-400" onclick="openZoom(this.querySelector('img').src)">
        <img src="{{ Storage::url($application->app_photo_passport) }}" alt="Паспорт" class="w-full h-32 object-cover">
        <p class="absolute bottom-0 w-full bg-black/60 text-white text-xs p-1 text-center">Паспорт</p>
      </div>
      @else
      <div class="rounded border-2 border-dashed border-gray-300 h-32 flex items-center justify-center"><span class="text-xs text-gray-400">Паспорт не загружен</span></div>
      @endif

      {{-- СНИЛС --}}
      @if($application->app_photo_snils)
      <div class="cursor-pointer group relative rounded overflow-hidden border hover:border-primary-400" onclick="openZoom(this.querySelector('img').src)">
        <img src="{{ Storage::url($application->app_photo_snils) }}" alt="СНИЛС" class="w-full h-32 object-cover">
        <p class="absolute bottom-0 w-full bg-black/60 text-white text-xs p-1 text-center">СНИЛС</p>
      </div>
      @else
      <div class="rounded border-2 border-dashed border-gray-300 h-32 flex items-center justify-center"><span class="text-xs text-gray-400">СНИЛС не загружен</span></div>
      @endif

      {{-- Аттестат — все загруженные страницы --}}
      @foreach(['app_photo_edu_1' => 'Аттестат стр.1', 'app_photo_edu_2' => 'Аттестат стр.2', 'app_photo_edu_3' => 'Приложение'] as $field => $label)
        @if($application->$field)
        <div class="cursor-pointer group relative rounded overflow-hidden border hover:border-primary-400" onclick="openZoom(this.querySelector('img').src)">
          <img src="{{ Storage::url($application->$field) }}" alt="{{ $label }}" class="w-full h-32 object-cover">
          <p class="absolute bottom-0 w-full bg-black/60 text-white text-xs p-1 text-center">{{ $label }}</p>
        </div>
        @endif
      @endforeach

      {{-- Подписанное заявление --}}
      @if($application->signed_doc_photo)
      <div class="cursor-pointer group relative rounded overflow-hidden border hover:border-primary-400 col-span-2" onclick="openZoom(this.querySelector('img').src)">
        <img src="{{ Storage::url($application->signed_doc_photo) }}" alt="Подписанное заявление" class="w-full h-48 object-cover">
        <p class="absolute bottom-0 w-full bg-black/60 text-white text-xs p-1 text-center">Подписанное заявление</p>
      </div>
      @endif
    </div>
    <div class="p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">⚠️ При отклонении или отправке на доработку обязателен комментарий с указанием причины.</div>
  </div>
</div>

{{-- Zoom overlay --}}
<div id="zoomOverlay" class="zoom-overlay" onclick="closeZoom(event)">
  <img id="zoomImg" src="" alt="">
  <button class="absolute top-4 right-4 text-white text-2xl bg-black/50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-black/70" onclick="event.stopPropagation();document.getElementById('zoomOverlay').classList.remove('active')">✕</button>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const checks = document.querySelectorAll('.review-check');
  const approveBtn = document.getElementById('approveBtn');
  checks.forEach(c => c.addEventListener('change', () => { approveBtn.disabled = ![...checks].every(x => x.checked); }));
  document.getElementById('reviewForm').addEventListener('submit', function(e) {
    const decision = e.submitter?.value;
    if ((decision === 'rejected' || decision === 'rework_needed') && !document.getElementById('rejection_reason').value.trim()) { e.preventDefault(); alert('Укажите комментарий сотрудника!'); }
  });
});
function openZoom(src) { document.getElementById('zoomImg').src = src; document.getElementById('zoomOverlay').classList.add('active'); }
function closeZoom(e) { if (e.target === document.getElementById('zoomOverlay')) document.getElementById('zoomOverlay').classList.remove('active'); }
</script>
@endsection
