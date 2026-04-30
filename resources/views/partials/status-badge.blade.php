@php
$config = [
    'draft' => ['text' => 'Черновик', 'class' => 'bg-gray-100 text-gray-800 border-gray-300'],
    'submitted' => ['text' => 'На проверке', 'class' => 'bg-blue-100 text-blue-800 border-blue-300'],
    'approved' => ['text' => 'Принято', 'class' => 'bg-green-100 text-green-800 border-green-300'],
    'rejected' => ['text' => 'Отклонено', 'class' => 'bg-red-100 text-red-800 border-red-300'],
    'rework_needed' => ['text' => 'На доработке', 'class' => 'bg-orange-100 text-orange-800 border-orange-300'],
    'cancelled' => ['text' => 'Отменено', 'class' => 'bg-gray-100 text-gray-500 border-gray-300'],
];
$s = $config[$status] ?? $config['draft'];
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $s['class'] }}">{{ $s['text'] }}</span>
