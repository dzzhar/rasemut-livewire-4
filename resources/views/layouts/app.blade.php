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

        <flux:main container class="max-w-4xl! space-y-8">
            {{ $slot }}
        </flux:main>

        @fluxScripts
    </body>

    </html>
