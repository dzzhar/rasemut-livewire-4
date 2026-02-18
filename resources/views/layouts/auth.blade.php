<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- title page --}}
    <title>{{ ($title ?? 'Login') . ' | ' . config('app.name') }}</title>

    {{-- scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="bg-white dark:bg-zinc-800 antialiased">
    {{ $slot }}

    @fluxScripts
</body>

</html>
