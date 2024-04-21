@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-neutral-400 text-start text-base font-medium text-neutral-700 bg-neutral-50 focus:outline-none focus:text-neutral-800 focus:bg-neutral-100 focus:border-neutral-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-neutral-600 hover:text-neutral-800 hover:bg-neutral-50 hover:border-neutral-300 focus:outline-none focus:text-neutral-800 focus:bg-neutral-50 focus:border-neutral-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
