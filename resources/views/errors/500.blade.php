<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- title page --}}
    <title>500 | {{ config('app.name') }}</title>

    {{-- scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="bg-white dark:bg-zinc-800 antialiased">
    <div class="min-h-screen flex items-center justify-center px-6">
        <div class="text-center max-w-lg">

            <h1
                class="text-[90px] sm:text-[120px] md:text-[150px] font-extrabold text-red-700 dark:text-red-500 leading-none">
                500
            </h1>

            <flux:heading size="lg" class="mt-4">
                Terjadi kesalahan pada server. <br>
                Silakan coba beberapa saat lagi atau hubungi administrator.
            </flux:heading>

            <div class="mt-8 flex justify-center gap-3">
                <flux:button onclick="location.reload()">Muat Ulang</flux:button>
                <flux:button onclick="history.back()">Kembali</flux:button>
            </div>

        </div>
    </div>

    @fluxScripts
</body>

</html>
