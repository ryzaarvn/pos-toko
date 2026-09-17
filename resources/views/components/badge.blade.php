@props(['status'])

@php
    $classes = match (strtolower($status)) {
        'aman' => 'bg-green-100 text-green-700',
        'menipis' => 'bg-yellow-100 text-yellow-700',
        'habis' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center rounded-full px-3 py-1
                text-xs font-semibold {$classes}"
]) }}>
    {{ $status }}
</span>