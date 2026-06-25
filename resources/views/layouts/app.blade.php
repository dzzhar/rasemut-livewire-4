<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- title page --}}
    <title>{{ ($title ?? 'Beranda') . ' | ' . config('app.name') }}</title>

    {{-- scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    @include('layouts.partials.header')
    @include('layouts.partials.sidebar')

    <flux:main container class="max-w-2xl! space-y-6">
        {{ $slot }}
    </flux:main>

    <livewire:action-modal />

    @persist('toast')
        <flux:toast.group position="top-center">
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
