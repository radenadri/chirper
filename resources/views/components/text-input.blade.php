@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'border-neutral-300 focus:border-neutral-500 focus:ring-neutral-500 rounded-md shadow-sm',
]) !!}>
