@props(['user', 'size' => 'md'])

@php
    $dimensions = match ($size) {
        'sm' => 'w-8 h-8',
        'lg' => 'w-16 h-16',
        default => 'w-10 h-10',
    };
    $pixels = match ($size) {
        'sm' => 32,
        'lg' => 64,
        default => 40,
    };
@endphp

<img src="{{ $user->avatarUrl($pixels) }}"
     alt="{{ $user->name }}"
     {{ $attributes->merge(['class' => "{$dimensions} rounded-full object-cover"]) }} />
