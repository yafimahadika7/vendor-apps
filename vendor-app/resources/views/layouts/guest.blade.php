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

<body class="min-h-screen font-sans text-white antialiased bg-gradient-to-b from-[#D7F5F9] via-[#F2FAFB] to-[#ffffff]">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="mb-2">
            <a href="/">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo" style="width: 200px;" class="mx-auto">
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white/90 shadow-xl border border-gray-200 rounded-xl">
            {{ $slot }}
        </div>
    </div>

</body>

</html>