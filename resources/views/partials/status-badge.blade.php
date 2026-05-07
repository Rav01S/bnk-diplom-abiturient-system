@php
$config = [
    'draft'         => ['text' => 'Черновик',     'class' => 'bg-gray-100 text-gray-600 border-gray-300'],
    'submitted'     => ['text' => 'На проверке',  'class' => 'bg-blue-100 text-blue-700 border-blue-200'],
    'approved'      => ['text' => 'Принято',       'class' => 'bg-green-100 text-green-700 border-green-200'],
    'rejected'      => ['text' => 'Отклонено',     'class' => 'bg-red-100 text-red-700 border-red-200'],
    'rework_needed' => ['text' => 'На доработке',  'class' => 'bg-amber-100 text-amber-700 border-amber-200'],
    'cancelled'     => ['text' => 'Отменено',      'class' => 'bg-gray-100 text-gray-500 border-gray-300'],
];
$s = $config[$status] ?? $config['draft'];
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $s['class'] }}">{{ $s['text'] }}</span>
