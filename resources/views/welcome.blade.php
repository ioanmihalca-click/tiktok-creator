<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Platformă de generare automată de videoclipuri TikTok folosind AI. Creează conținut viral în minute.">
    <title>TikTok Creator AI - Generare Automată de Conținut Viral</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/favicon/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/favicon/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png') }}">
    <meta name="apple-mobile-web-app-title" content="TikTok-Creator">
    <link rel="manifest" href="{{ asset('assets/favicon/site.webmanifest') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-[#0A0A0F] text-white min-h-screen selection:bg-purple-500/30 selection:text-white">
    <!-- Decorative gradient effects -->
    <div
        class="fixed inset-0 -z-10 h-screen w-full bg-[#0A0A0F] bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.3),rgba(255,255,255,0))] pointer-events-none">
    </div>

    <div class="relative min-h-screen">
        <!-- Hero Section -->
        <div class="container max-w-6xl px-4 py-4 mx-auto sm:py-6">
            <div class="text-center">
                <img src="{{ asset('assets/logo-transparent.webp') }}" alt="TikTok-Creator AI Logo"
                    class="h-24 mx-auto mb-3 transition-transform duration-300 transform sm:h-32 hover:scale-105 hover:rotate-2">

                <!-- Main Content Container -->
                <div class="max-w-2xl mx-auto space-y-6 sm:space-y-8">
                    <!-- Beta Badge -->
                    <div
                        class="inline-flex items-center px-3 py-1.5 space-x-2 text-xs sm:text-sm font-medium text-yellow-200 transition-all duration-300 rounded-full bg-yellow-900/20 hover:bg-yellow-900/30">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                        </svg>
                        <span>Versiune Beta</span>
                    </div>

                    <!-- Beta Message -->
                    <div
                        class="p-4 transition-all duration-300 border sm:p-6 rounded-2xl bg-gradient-to-br from-purple-900/30 to-blue-900/30 backdrop-blur-sm border-white/10 hover:border-white/20">
                        <h1
                            class="mb-4 sm:mb-6 text-3xl sm:text-4xl md:text-5xl font-bold text-transparent bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 bg-clip-text [text-shadow:0_4px_8px_rgba(0,0,0,0.1)]">
                            Creează filmulețe virale pentru TikTok
                        </h1>
                        <p class="max-w-2xl mx-auto mb-6 text-lg text-gray-300 sm:mb-8 sm:text-xl">
                            Transformă-ți ideile în conținut viral folosind puterea inteligenței artificiale.
                            Totul în mai puțin de 5 minute.
                        </p>

                        <div class="space-y-3 text-sm text-gray-300 sm:space-y-4 sm:text-base">
                            <p class="flex items-center justify-center gap-2">
                                <svg class="flex-shrink-0 w-5 h-5 text-purple-400 animate-pulse" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Aplicația TikTok-Creator este în versiune beta si se poate testa gratuit
                            </p>
                            <p class="flex items-center justify-center gap-2">
                                <svg class="flex-shrink-0 w-5 h-5 text-pink-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                </svg>
                                Filmulețele generate vor avea logoul TikTok-Creator și Shotstack
                            </p>
                            <div class="flex flex-col items-center gap-4">
                                <p class="flex items-center justify-center gap-2">
                                    <svg class="flex-shrink-0 w-5 h-5 text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Așteptăm sugestiile voastre de îmbunătățire!
                                </p>

                                <div class="flex items-center gap-4 text-sm">
                                    <a href="mailto:contact@tiktok-creator.ro"
                                        class="flex items-center gap-2 text-purple-400 transition-colors hover:text-purple-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        contact@tiktok-creator.ro
                                    </a>
                                    <span class="text-gray-600">|</span>
                                    <a href="https://www.tiktok.com/@creatorul_de_tiktokuri"
                                        class="flex items-center gap-2 text-pink-400 transition-colors hover:text-pink-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M21 7h-3V6a3 3 0 0 0-3-3H9a3 3 0 0 0-3 3v1H3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1zM8 6a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v1H8V6zm12 13H4V9h16v10z" />
                                        </svg>
                                        @creatorul_de_tiktokuri
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>


                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-medium text-white transition-all duration-300 transform border border-transparent sm:px-8 sm:py-4 sm:text-lg group rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 hover:scale-105 hover:shadow-lg hover:shadow-purple-500/25">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Creează TikTok
                            </span>
                        </a>
                    @else
                        <div class="flex flex-col items-center justify-center gap-4 sm:gap-6 sm:flex-row">
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-medium text-white transition-all duration-300 transform border border-transparent sm:px-8 sm:py-4 sm:text-lg group rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 hover:scale-105 hover:shadow-lg hover:shadow-purple-500/25">
                                Începe Gratuit
                            </a>
                            <a href="#how-it-works"
                                class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-medium text-gray-300 transition-all duration-300 border sm:px-8 sm:py-4 sm:text-lg group rounded-xl border-white/10 hover:bg-white/10 hover:text-white hover:border-white/20">
                                Cum Funcționează
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Process Steps -->
        <section id="how-it-works" class="py-16 sm:py-24 bg-gradient-to-b from-transparent to-black/30">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-12 text-center sm:mb-16">
                    <h2
                        class="mb-4 text-2xl sm:text-3xl md:text-4xl font-bold [text-shadow:0_4px_8px_rgba(0,0,0,0.1)]">
                        Procesul de Creare
                    </h2>
                    <p class="text-sm text-gray-400 sm:text-base">De la idee la TikTok viral în 4 pași simpli</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Step 1: Script -->
                    <div
                        class="relative p-6 transition-all duration-300 border group bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm hover:border-purple-500/30 hover:bg-white/10">
                        <div
                            class="absolute flex items-center justify-center w-8 h-8 text-lg font-bold transition-transform duration-300 bg-purple-600 rounded-full -top-4 left-4 group-hover:scale-110">
                            1
                        </div>
                        <div class="pt-4">
                            <h3 class="mb-4 text-xl font-semibold text-purple-400 group-hover:text-purple-300">Generare
                                Script</h3>
                            <p class="text-gray-400 group-hover:text-gray-300">AI-ul nostru analizează tendințele și
                                generează un
                                script optimizat pentru engagement.</p>
                        </div>
                    </div>

                    <!-- Step 2: Image -->
                    <div
                        class="relative p-6 transition-all duration-300 border group bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm hover:border-blue-500/30 hover:bg-white/10">
                        <div
                            class="absolute flex items-center justify-center w-8 h-8 text-lg font-bold transition-transform duration-300 bg-blue-600 rounded-full -top-4 left-4 group-hover:scale-110">
                            2
                        </div>
                        <div class="pt-4">
                            <h3 class="mb-4 text-xl font-semibold text-blue-400 group-hover:text-blue-300">Generare
                                Imagini</h3>
                            <p class="text-gray-400 group-hover:text-gray-300">Creăm imagini atractive și relevante
                                pentru conținutul
                                tău folosind AI.</p>
                        </div>
                    </div>

                    <!-- Step 3: Audio -->
                    <div
                        class="relative p-6 transition-all duration-300 border group bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm hover:border-pink-500/30 hover:bg-white/10">
                        <div
                            class="absolute flex items-center justify-center w-8 h-8 text-lg font-bold transition-transform duration-300 bg-pink-600 rounded-full -top-4 left-4 group-hover:scale-110">
                            3
                        </div>
                        <div class="pt-4">
                            <h3 class="mb-4 text-xl font-semibold text-pink-400 group-hover:text-pink-300">Narare Audio
                            </h3>
                            <p class="text-gray-400 group-hover:text-gray-300">Transformăm scriptul în narare audio
                                naturală și
                                captivantă.</p>
                        </div>
                    </div>

                    <!-- Step 4: Video -->
                    <div
                        class="relative p-6 transition-all duration-300 border group bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm hover:border-green-500/30 hover:bg-white/10">
                        <div
                            class="absolute flex items-center justify-center w-8 h-8 text-lg font-bold transition-transform duration-300 bg-green-600 rounded-full -top-4 left-4 group-hover:scale-110">
                            4
                        </div>
                        <div class="pt-4">
                            <h3 class="mb-4 text-xl font-semibold text-green-400 group-hover:text-green-300">Asamblare
                                Video</h3>
                            <p class="text-gray-400 group-hover:text-gray-300">Combinăm toate elementele într-un TikTok
                                profesional
                                gata de postare.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-16 sm:py-24">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-12 text-center sm:mb-16">
                    <h2
                        class="mb-4 text-2xl sm:text-3xl md:text-4xl font-bold [text-shadow:0_4px_8px_rgba(0,0,0,0.1)]">
                        Planuri Simple
                    </h2>
                    <p class="text-sm text-gray-400 sm:text-base">Alege planul perfect pentru nevoile tale</p>
                </div>

                <div class="grid max-w-4xl grid-cols-1 gap-6 mx-auto sm:gap-8 md:grid-cols-2">
                    <!-- Free Plan -->
                    <div class="p-8 border bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm">
                        <h3 class="mb-4 text-2xl font-bold">Free</h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">0 RON</span>
                            <span class="text-gray-400">/lună</span>
                        </div>
                        <ul class="mb-8 space-y-4">
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>5 videoclipuri/lună</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Rezoluție 720p</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Watermark inclus</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="block w-full px-6 py-3 text-lg font-medium text-center text-white transition-all duration-200 rounded-xl bg-white/10 hover:bg-white/20">
                            Începe Gratuit
                        </a>
                    </div>

                    <!-- Premium Plan -->
                    <div
                        class="relative p-8 overflow-hidden transition-all duration-300 border group bg-gradient-to-br from-purple-900/30 to-blue-900/30 rounded-2xl border-purple-500/50 backdrop-blur-sm hover:border-purple-500/70 hover:from-purple-900/40 hover:to-blue-900/40">
                        <!-- Popular Badge -->
                        <div
                            class="absolute top-0 right-0 px-4 py-1 text-sm font-medium text-white transition-all duration-300 bg-purple-500 rounded-bl-lg group-hover:bg-purple-600">
                            Popular
                        </div>

                        <h3 class="mb-4 text-2xl font-bold">Premium (in curand)</h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">49 RON</span>
                            <span class="text-gray-400">/lună</span>
                        </div>
                        <ul class="mb-8 space-y-4">
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Videoclipuri nelimitate</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Mai multe voci de narare</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Postare automata pe TikTok</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Rezoluție HD</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fără watermark</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Suport prioritar</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="block w-full px-6 py-3 text-lg font-medium text-center text-white transition-all duration-300 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 hover:shadow-lg hover:shadow-purple-500/25">
                            Încearcă Premium
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <x-footer />
    </div>

    <style>
        @layer utilities {
            .text-shadow {
                text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</body>

</html>
