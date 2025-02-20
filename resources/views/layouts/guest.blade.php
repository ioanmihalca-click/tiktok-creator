<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/favicon/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/favicon/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png') }}">
    <meta name="apple-mobile-web-app-title" content="TikTok-Creator">
    <link rel="manifest" href="{{ asset('assets/favicon/site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-[#0A0A0F] text-white min-h-screen">
    <!-- Decorative gradient effects -->
    <div
        class="fixed inset-0 -z-10 h-screen w-full bg-[#0A0A0F] bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.3),rgba(255,255,255,0))]">
    </div>

    <div class="flex flex-col items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <a href="/" wire:navigate
                    class="inline-block transition-transform duration-300 transform hover:scale-105">
                    <img src="{{ asset('assets/logo-transparent.webp') }}" alt="TikTok Maker AI Logo"
                        class="h-20 mx-auto">
                </a>
            </div>

            <!-- Main Content -->
            {{ $slot }}

            <!-- Back to home link -->
            <div class="mt-6 text-center">
                <a href="/" wire:navigate
                    class="text-sm text-gray-400 transition-colors duration-200 hover:text-purple-400">
                    ← Înapoi la pagina principală
                </a>
            </div>
        </div>
    </div>

    <x-footer />
</body>

</html>
