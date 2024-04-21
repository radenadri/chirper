<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-neutral-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-12 bg-neutral-100">
        <div class="flex flex-col gap-3 items-center">
            <a href="/" wire:navigate>
                <x-application-logo class="w-20 h-20 fill-current text-neutral-500" />
            </a>
            <div>
                <x-nav-link :href="route('welcome')" wire:navigate>
                    {{ __('Homepage') }}
                </x-nav-link>
                <x-nav-link href="#" wire:navigate>
                    {{ __('About') }}
                </x-nav-link>
            </div>
        </div>

        <div class="w-full sm:max-w-2xl px-6 py-4 overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>
@livewireScriptConfig
@stack('scripts')

</html>
