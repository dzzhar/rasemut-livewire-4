<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- title page --}}
    <title>404 | {{ config('app.name') }}</title>

    {{-- scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="bg-white dark:bg-zinc-800 antialiased">
    <div class="min-h-screen flex items-center justify-center px-6">
        <div class="text-center max-w-lg">

            <h1
                class="text-[90px] sm:text-[120px] md:text-[150px] font-extrabold text-blue-700 dark:text-blue-500 leading-none">
                404
            </h1>

            <flux:heading size="lg" class="mt-4">
                Halaman yang Anda cari tidak ditemukan. <br>
                Periksa kembali URL atau kembali ke halaman sebelumnya.
            </flux:heading>

            <div class="mt-8 flex justify-center gap-3">
                <flux:button onclick="history.back()">Kembali</flux:button>
            </div>

        </div>
    </div>

    @fluxScripts
</body>

</html>
