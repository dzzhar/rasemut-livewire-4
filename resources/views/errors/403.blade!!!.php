<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- title page --}}
    <title>403 | {{ config('app.name') }}</title>

    {{-- scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="bg-white dark:bg-zinc-800 antialiased">
    <div class="min-h-screen flex items-center justify-center px-6">
        <div class="text-center max-w-lg">

            <h1
                class="text-[90px] sm:text-[120px] md:text-[150px] font-extrabold text-orange-700 dark:text-orange-500 leading-none">
                403
            </h1>

            <flux:heading size="lg" class="mt-4">
                Anda tidak memiliki izin untuk mengakses halaman ini. <br>
                Silakan hubungi administrator sistem apabila Anda memerlukan akses.
            </flux:heading>

            <div class="mt-8">
                <flux:button onclick="history.back()">Kembali</flux:button>
            </div>

        </div>
    </div>

    @fluxScripts
</body>

</html>
