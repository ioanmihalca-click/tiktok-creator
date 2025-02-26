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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|inter:300,400,500,600,700,800&display=swap"
        rel="stylesheet" />
    <!-- Add modern icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Adăugare AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @livewireStyles
</head>

<body
    class="antialiased bg-[#0A0A0F] text-white min-h-screen selection:bg-purple-500/30 selection:text-white font-inter">
    <!-- Decorative gradients with more refined animation -->
    <div
        class="fixed inset-0 -z-10 h-screen w-full bg-[#0A0A0F] bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.3),rgba(255,255,255,0))] pointer-events-none">
    </div>

    <!-- Animated noise texture overlay -->
    <div class="fixed inset-0 pointer-events-none -z-5 opacity-20">
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <filter id="noise">
                <feTurbulence type="fractalNoise" baseFrequency="0.80" numOctaves="4" stitchTiles="stitch" />
                <feColorMatrix type="saturate" values="0" />
            </filter>
            <rect width="100%" height="100%" filter="url(#noise)" opacity="0.15" />
        </svg>
    </div>

    <!-- Modern Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 border-b bg-black/30 backdrop-blur-md border-white/10">
        <div class="container max-w-6xl px-4 py-4 mx-auto">
            <div class="flex items-center justify-between">
                <a href="{{ url('/') }}" class="transition-all duration-300 transform hover:scale-105">
                    <img src="{{ asset('assets/logo-transparent.webp') }}" alt="TikTok-Creator Logo" class="h-10">
                </a>
                <div class="items-center hidden space-x-6 text-sm sm:flex">
                    <a href="#how-it-works" class="text-gray-300 transition-all hover:text-white">Cum funcționează</a>
                    <a href="#features" class="text-gray-300 transition-all hover:text-white">Funcționalități</a>
                    <a href="#pricing" class="text-gray-300 transition-all hover:text-white">Prețuri</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium text-white transition-all rounded-full bg-purple-600/70 hover:bg-purple-600">
                            <i class="mr-1 ri-dashboard-fill"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="hidden text-gray-300 transition-all sm:inline-flex hover:text-white">Login</a>
                        <a href="{{ route('register') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium text-white transition-all rounded-full bg-purple-600/70 hover:bg-purple-600">
                            <i class="mr-1 ri-user-add-line"></i> Înregistrare
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="relative min-h-screen">
        <!-- Hero Section with animated elements -->
        <div class="container max-w-6xl px-4 pt-32 pb-20 mx-auto sm:pt-40 sm:pb-28">
            <div class="text-center" data-aos="fade-up" data-aos-duration="1000">
                <!-- Badge with pulse animation -->
                <div
                    class="inline-flex items-center px-3 py-1.5 mb-8 space-x-2 text-xs sm:text-sm font-medium text-purple-200 transition-all duration-300 rounded-full bg-purple-900/50 border border-purple-500/50 shadow-lg shadow-purple-900/20">
                    <i class="text-yellow-400 ri-rocket-2-fill"></i>
                    <span>Versiune Beta</span>
                </div>

                <h1
                    class="mb-6 text-4xl font-bold leading-tight tracking-tight text-transparent sm:text-5xl md:text-6xl bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 bg-clip-text">
                    TikTok Viral <br class="sm:hidden">în <span class="relative inline-block">
                        <span class="ml-2 highlight-text">Minute</span>
                        <span
                            class="absolute bottom-0 left-0 w-full h-3 transform bg-gradient-to-r from-purple-600/40 to-pink-600/40 -z-10 -rotate-1"></span>
                    </span>
                </h1>

                <p class="max-w-2xl mx-auto mb-10 text-xl leading-relaxed text-gray-300 sm:text-2xl" data-aos="fade-up"
                    data-aos-delay="100">
                    Transformă-ți ideile în conținut viral folosind puterea inteligenței artificiale.
                    <span
                        class="font-medium text-transparent bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text">100%
                        automatizat.</span>
                </p>

                <!-- CTA Section with 3D animation -->
                <div class="flex flex-col items-center justify-center gap-4 sm:gap-6 sm:flex-row" data-aos="fade-up"
                    data-aos-delay="200">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="relative inline-flex items-center justify-center w-full px-8 py-4 overflow-hidden text-lg font-medium text-white transition-all duration-300 shadow-lg group sm:w-auto rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 shadow-purple-600/30 hover:shadow-xl hover:shadow-purple-600/40 hover:scale-105">
                            <span
                                class="absolute inset-0 w-full h-full bg-gradient-to-r from-purple-600/0 via-white/20 to-purple-600/0 transform translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></span>
                            <span class="relative flex items-center">
                                <i class="mr-2 ri-movie-fill"></i> Creează TikTok
                            </span>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="relative inline-flex items-center justify-center w-full px-8 py-4 overflow-hidden text-lg font-medium text-white transition-all duration-300 shadow-lg group sm:w-auto rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 shadow-purple-600/30 hover:shadow-xl hover:shadow-purple-600/40 hover:scale-105">
                            <span
                                class="absolute inset-0 w-full h-full bg-gradient-to-r from-purple-600/0 via-white/20 to-purple-600/0 transform translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></span>
                            <span class="relative flex items-center">
                                <i class="mr-2 ri-vip-crown-fill"></i> Începe Gratuit
                            </span>
                        </a>
                        <a href="#how-it-works"
                            class="inline-flex items-center justify-center w-full px-8 py-4 text-base font-medium text-gray-300 transition-all duration-300 border sm:w-auto rounded-xl border-white/10 backdrop-blur-sm hover:bg-white/10 hover:text-white hover:shadow-lg">
                            <i class="mr-2 ri-play-circle-line"></i> Vezi Demo
                        </a>
                    @endauth
                </div>

                <!-- Stats Section -->
                <div class="grid max-w-3xl grid-cols-2 gap-6 mx-auto mt-16 sm:grid-cols-3 lg:grid-cols-3"
                    data-aos="fade-up" data-aos-delay="300">
                    <div class="p-6 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur-sm">
                        <span class="block text-3xl font-bold text-purple-400 sm:text-4xl">500+</span>
                        <span class="text-gray-400">Utilizatori Activi</span>
                    </div>
                    <div class="p-6 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur-sm">
                        <span class="block text-3xl font-bold text-pink-400 sm:text-4xl">2.5K+</span>
                        <span class="text-gray-400">TikTok-uri Generate</span>
                    </div>
                    <div class="p-6 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur-sm">
                        <span class="block text-3xl font-bold text-blue-400 sm:text-4xl">98%</span>
                        <span class="text-gray-400">Satisfacție Clienți</span>
                    </div>
                </div>

                <!-- Mockup Device Section -->
                <div class="relative max-w-3xl mx-auto mt-20 overflow-hidden border shadow-2xl rounded-2xl shadow-purple-600/20 border-white/10"
                    data-aos="zoom-in-up" data-aos-delay="400">
                    <div
                        class="flex items-center justify-center aspect-video bg-gradient-to-br from-purple-900/80 via-black to-pink-900/80">
                        <!-- Placeholder for app mockup image -->
                        <div class="flex flex-col items-center p-8 text-center">
                            <i class="mb-4 text-6xl ri-smartphone-line text-white/80"></i>
                            <p class="text-xl font-medium text-white">Aplicația TikTok Creator în acțiune</p>
                            <p class="mt-2 text-sm text-gray-400">Interfață intuitivă pentru creare rapidă</p>
                            <div class="mt-6">
                                <button
                                    class="flex items-center px-5 py-2.5 text-sm font-medium rounded-full bg-white/20 hover:bg-white/30 transition-all">
                                    <i class="ri-play-fill mr-1.5"></i> Vezi Demo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Features Section (New) -->
        <section id="features" class="relative py-20 overflow-hidden sm:py-28">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

            <div class="relative px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-16 text-center" data-aos="fade-up">
                    <span
                        class="px-4 py-1.5 text-xs font-medium text-white rounded-full bg-gradient-to-r from-purple-600/80 to-blue-600/80 inline-block mb-4">CARACTERISTICI
                        PRINCIPALE</span>
                    <h2
                        class="mb-4 text-3xl font-bold text-transparent sm:text-4xl md:text-5xl bg-gradient-to-r from-white to-gray-300 bg-clip-text">
                        De ce să alegi TikTok Creator AI
                    </h2>
                    <p class="max-w-2xl mx-auto text-gray-400">Platforma noastră oferă tot ce ai nevoie pentru a crea
                        conținut viral, rapid și eficient</p>
                </div>

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3" data-aos="fade-up"
                    data-aos-delay="100">
                    <!-- Feature 1 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10 hover:-translate-y-1">
                        <div
                            class="flex items-center justify-center w-12 h-12 mb-6 shadow-lg bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl shadow-purple-600/20">
                            <i class="text-xl ri-ai-generate"></i>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-white">AI Avansat</h3>
                        <p class="text-gray-400">Algoritmi de ultimă generație pentru crearea de conținut optimizat
                            pentru angajament și viralitate.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10 hover:-translate-y-1">
                        <div
                            class="flex items-center justify-center w-12 h-12 mb-6 shadow-lg bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl shadow-blue-600/20">
                            <i class="text-xl ri-time-line"></i>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-white">Rapid și Eficient</h3>
                        <p class="text-gray-400">Generează videoclipuri complete în mai puțin de 5 minute, economisind
                            ore de editare manuală.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10 hover:-translate-y-1">
                        <div
                            class="flex items-center justify-center w-12 h-12 mb-6 shadow-lg bg-gradient-to-br from-pink-600 to-red-600 rounded-xl shadow-pink-600/20">
                            <i class="text-xl ri-voice-recognition-line"></i>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-white">Voci Naturale</h3>
                        <p class="text-gray-400">Narațiune audio realistă cu intonație și accentuare perfectă pentru
                            conținutul tău.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10 hover:-translate-y-1">
                        <div
                            class="flex items-center justify-center w-12 h-12 mb-6 shadow-lg bg-gradient-to-br from-green-600 to-teal-600 rounded-xl shadow-green-600/20">
                            <i class="text-xl ri-trend-chart-line"></i>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-white">Analize de Tendințe</h3>
                        <p class="text-gray-400">Algoritmi care monitorizează și analizează cele mai recente tendințe
                            TikTok pentru conținut relevant.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10 hover:-translate-y-1">
                        <div
                            class="flex items-center justify-center w-12 h-12 mb-6 shadow-lg bg-gradient-to-br from-amber-600 to-yellow-600 rounded-xl shadow-amber-600/20">
                            <i class="text-xl ri-image-2-line"></i>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-white">Imagini de Calitate</h3>
                        <p class="text-gray-400">Generare automată de imagini relevante și vizual atractive pentru
                            videoclipurile tale.</p>
                    </div>

                    <!-- Feature 6 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10 hover:-translate-y-1">
                        <div
                            class="flex items-center justify-center w-12 h-12 mb-6 shadow-lg bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl shadow-purple-600/20">
                            <i class="text-xl ri-shield-check-line"></i>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-white">Siguranță și Conformitate</h3>
                        <p class="text-gray-400">Conținut generat în conformitate cu regulile TikTok pentru a evita
                            restricțiile.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Process Steps with enhanced animations -->
        <section id="how-it-works" class="relative overflow-hidden py-28 sm:py-36">
            <div class="absolute inset-0 bg-gradient-to-b from-black/20 to-[#0A0A0F]"></div>

            <div class="relative px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-20 text-center" data-aos="fade-up">
                    <span
                        class="px-4 py-1.5 text-xs font-medium text-white rounded-full bg-gradient-to-r from-blue-600/80 to-purple-600/80 inline-block mb-4">PROCES
                        SIMPLU</span>
                    <h2
                        class="mb-4 text-3xl font-bold text-transparent sm:text-4xl md:text-5xl bg-gradient-to-r from-white to-gray-300 bg-clip-text">
                        De la Idee la TikTok Viral
                    </h2>
                    <p class="max-w-2xl mx-auto text-gray-400">Sistem complet automatizat în doar 4 pași simpli</p>
                </div>

                <!-- Process Steps Timeline -->
                <div class="relative max-w-4xl mx-auto">
                    <!-- Timeline line -->
                    <div
                        class="absolute left-4 sm:left-1/2 top-0 h-full w-0.5 bg-gradient-to-b from-purple-500 via-pink-500 to-blue-500 transform sm:-translate-x-1/2">
                    </div>

                    <!-- Step 1 -->
                    <div class="relative mb-16 sm:mb-24" data-aos="fade-right">
                        <div class="flex flex-col items-start sm:flex-row sm:items-center">
                            <div
                                class="z-10 flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-purple-600 rounded-full shadow-lg shadow-purple-800/30">
                                1
                            </div>
                            <div class="flex-1 ml-6 sm:ml-12">
                                <div
                                    class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-sm sm:ml-6 hover:bg-white/8 hover:shadow-xl hover:shadow-purple-900/10">
                                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-16 h-16 shadow-lg bg-gradient-to-br from-purple-600/90 to-blue-600/90 rounded-xl shadow-purple-900/30">
                                            <i class="text-3xl ri-file-text-line"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-3 text-xl font-semibold text-purple-400">Generare Script</h3>
                                            <p class="text-gray-400">AI-ul nostru analizează tendințele curente și
                                                generează un script optimizat pentru engagement bazat pe tema aleasă.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative mb-16 sm:mb-24" data-aos="fade-left">
                        <div class="flex flex-col items-start sm:flex-row-reverse sm:items-center">
                            <div
                                class="z-10 flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-blue-600 rounded-full shadow-lg shadow-blue-800/30">
                                2
                            </div>
                            <div class="flex-1 ml-6 sm:ml-12 sm:mr-12 sm:text-right">
                                <div
                                    class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-sm hover:bg-white/8 hover:shadow-xl hover:shadow-blue-900/10">
                                    <div class="flex flex-col gap-6 sm:flex-row-reverse sm:items-center">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-16 h-16 shadow-lg bg-gradient-to-br from-blue-600/90 to-cyan-600/90 rounded-xl shadow-blue-900/30">
                                            <i class="text-3xl ri-image-line"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-3 text-xl font-semibold text-blue-400">Generare Imagini</h3>
                                            <p class="text-gray-400">Creăm imagini atractive și relevante pentru
                                                conținutul tău folosind tehnologii avansate de AI.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative mb-16 sm:mb-24" data-aos="fade-right">
                        <div class="flex flex-col items-start sm:flex-row sm:items-center">
                            <div
                                class="z-10 flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-pink-600 rounded-full shadow-lg shadow-pink-800/30">
                                3
                            </div>
                            <div class="flex-1 ml-6 sm:ml-12">
                                <div
                                    class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-sm sm:ml-6 hover:bg-white/8 hover:shadow-xl hover:shadow-pink-900/10">
                                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-16 h-16 shadow-lg bg-gradient-to-br from-pink-600/90 to-red-600/90 rounded-xl shadow-pink-900/30">
                                            <i class="text-3xl ri-volume-up-line"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-3 text-xl font-semibold text-pink-400">Narare Audio</h3>
                                            <p class="text-gray-400">Transformăm scriptul în narare audio naturală cu
                                                intonație perfectă și voci optimizate pentru TikTok.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative" data-aos="fade-left">
                        <div class="flex flex-col items-start sm:flex-row-reverse sm:items-center">
                            <div
                                class="z-10 flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-green-600 rounded-full shadow-lg shadow-green-800/30">
                                4
                            </div>
                            <div class="flex-1 ml-6 sm:ml-12 sm:mr-12 sm:text-right">
                                <div
                                    class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-sm hover:bg-white/8 hover:shadow-xl hover:shadow-green-900/10">
                                    <div class="flex flex-col gap-6 sm:flex-row-reverse sm:items-center">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-16 h-16 shadow-lg bg-gradient-to-br from-green-600/90 to-teal-600/90 rounded-xl shadow-green-900/30">
                                            <i class="text-3xl ri-movie-2-line"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-3 text-xl font-semibold text-green-400">Asamblare Video</h3>
                                            <p class="text-gray-400">Combinăm toate elementele într-un TikTok
                                                profesional cu efecte, timpi și tranziții optimizate pentru engagement.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Testimonials Section (New) -->
        <section class="relative py-20 overflow-hidden sm:py-28">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/70"></div>

            <div class="relative px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-16 text-center" data-aos="fade-up">
                    <span
                        class="px-4 py-1.5 text-xs font-medium text-white rounded-full bg-gradient-to-r from-pink-600/80 to-purple-600/80 inline-block mb-4">TESTIMONIALE</span>
                    <h2
                        class="mb-4 text-3xl font-bold text-transparent sm:text-4xl md:text-5xl bg-gradient-to-r from-white to-gray-300 bg-clip-text">
                        Ce Spun Utilizatorii Noștri
                    </h2>
                    <p class="max-w-2xl mx-auto text-gray-400">Iată câteva din experiențele celor care folosesc deja
                        platforma noastră</p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-3" data-aos="fade-up" data-aos-delay="100">
                    <!-- Testimonial 1 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10">
                        <div class="flex items-center mb-4">
                            <div
                                class="w-12 h-12 overflow-hidden rounded-full bg-gradient-to-br from-purple-600 to-pink-600">
                                <div class="flex items-center justify-center w-full h-full text-white">
                                    <i class="text-xl ri-user-smile-line"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-white">Alexandra M.</h4>
                                <p class="text-sm text-gray-400">Creator de conținut</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="flex text-yellow-400">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                        </div>
                        <p class="italic text-gray-300">"Am economisit ore de editare cu acest instrument. Filmulețele
                            generate au un aspect profesional și primesc mult mai multe vizualizări față de cele pe care
                            le făceam manual."</p>
                    </div>

                    <!-- Testimonial 2 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10">
                        <div class="flex items-center mb-4">
                            <div
                                class="w-12 h-12 overflow-hidden rounded-full bg-gradient-to-br from-blue-600 to-cyan-600">
                                <div class="flex items-center justify-center w-full h-full text-white">
                                    <i class="text-xl ri-user-smile-line"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-white">Mihai D.</h4>
                                <p class="text-sm text-gray-400">Antreprenor</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="flex text-yellow-400">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-half-fill"></i>
                            </div>
                        </div>
                        <p class="italic text-gray-300">"Incredibil cât de rapid pot crea conținut pentru brand-ul meu.
                            Rezultatele sunt peste așteptări, iar audiența mea crește constant cu fiecare TikTok
                            postat."</p>
                    </div>

                    <!-- Testimonial 3 -->
                    <div
                        class="p-6 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-md hover:border-purple-500/30 hover:bg-white/8 hover:shadow-lg hover:shadow-purple-500/10">
                        <div class="flex items-center mb-4">
                            <div
                                class="w-12 h-12 overflow-hidden rounded-full bg-gradient-to-br from-pink-600 to-red-600">
                                <div class="flex items-center justify-center w-full h-full text-white">
                                    <i class="text-xl ri-user-smile-line"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-white">Elena P.</h4>
                                <p class="text-sm text-gray-400">Influencer</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="flex text-yellow-400">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                        </div>
                        <p class="italic text-gray-300">"De când folosesc TikTok Creator AI, numărul de urmăritori a
                            crescut cu 400% în doar două luni. Este uimitor cât de bine știe să creeze conținut care
                            rezonează cu publicul."</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section with improved card design -->
        <section id="pricing" class="relative py-20 overflow-hidden sm:py-28">
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/80"></div>

            <div class="relative px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-16 text-center" data-aos="fade-up">
                    <span
                        class="px-4 py-1.5 text-xs font-medium text-white rounded-full bg-gradient-to-r from-green-600/80 to-blue-600/80 inline-block mb-4">PREȚURI
                        TRANSPARENTE</span>
                    <h2
                        class="mb-4 text-3xl font-bold text-transparent sm:text-4xl md:text-5xl bg-gradient-to-r from-white to-gray-300 bg-clip-text">
                        Planuri Simple
                    </h2>
                    <p class="max-w-2xl mx-auto text-gray-400">Alege planul perfect pentru nevoile tale de creare de
                        conținut</p>
                </div>

                <div class="grid max-w-4xl grid-cols-1 gap-8 mx-auto sm:gap-10 md:grid-cols-2" data-aos="fade-up"
                    data-aos-delay="100">
                    <!-- Free Plan with enhanced styling -->
                    <div
                        class="p-8 transition-all duration-500 border rounded-2xl bg-white/5 border-white/10 backdrop-blur-sm hover:border-white/20 hover:shadow-xl hover:shadow-purple-900/10 hover:bg-white/8 hover:-translate-y-1 group">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold transition-colors duration-300 group-hover:text-purple-300">
                                Free</h3>
                            <span
                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-white/10 text-gray-300">Beta</span>
                        </div>
                        <div class="mb-8">
                            <span
                                class="text-4xl font-bold transition-colors duration-300 group-hover:text-purple-300">0
                                RON</span>
                            <span class="text-gray-400">/lună</span>
                            <p class="mt-2 text-sm text-gray-400">Perfect pentru începători și testare</p>
                        </div>
                        <ul class="mb-8 space-y-4">
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-110"></i>
                                <span class="transition-colors duration-300 group-hover:text-gray-300">5
                                    videoclipuri/lună</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-110"></i>
                                <span class="transition-colors duration-300 group-hover:text-gray-300">Rezoluție
                                    720p</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-110"></i>
                                <span class="transition-colors duration-300 group-hover:text-gray-300">Watermark
                                    inclus</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-110"></i>
                                <span class="transition-colors duration-300 group-hover:text-gray-300">Acces la
                                    funcțiile de bază</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="relative block w-full px-6 py-3 overflow-hidden text-lg font-medium text-center text-white transition-all duration-300 rounded-xl bg-white/10 hover:bg-white/20 hover:shadow-lg hover:shadow-white/5 group-hover:bg-gradient-to-r group-hover:from-purple-600/80 group-hover:to-blue-600/80">
                            <span>Începe Gratuit</span>
                        </a>
                    </div>

                    <!-- Premium Plan with enhanced styling and effects -->
                    <div
                        class="relative p-8 overflow-hidden transition-all duration-500 border group bg-gradient-to-br from-purple-900/40 to-blue-900/40 rounded-2xl border-purple-500/50 backdrop-blur-sm hover:border-purple-500/80 hover:from-purple-900/50 hover:to-blue-900/50 hover:shadow-2xl hover:shadow-purple-900/20 hover:-translate-y-1">
                        <!-- Popular Badge with glow -->
                        <div
                            class="absolute top-0 right-0 px-4 py-1 text-sm font-medium text-white transition-all duration-300 rounded-bl-lg shadow-lg bg-gradient-to-r from-purple-600 to-pink-600 shadow-purple-500/30">
                            Popular
                        </div>

                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-purple-300">Premium</h3>
                            <span class="px-3 py-1.5 text-xs font-medium rounded-full bg-white/10 text-gray-300">În
                                curând</span>
                        </div>
                        <div class="mb-8">
                            <span class="text-4xl font-bold text-purple-300">49 RON</span>
                            <span class="text-gray-400">/lună</span>
                            <p class="mt-2 text-sm text-gray-400">Pentru creatori serioși și business</p>
                        </div>
                        <ul class="mb-8 space-y-4">
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-125"></i>
                                <span class="text-gray-300">Videoclipuri nelimitate</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-125"></i>
                                <span class="text-gray-300">Mai multe voci și accente</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-125"></i>
                                <span class="text-gray-300">Postare automată pe TikTok</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-125"></i>
                                <span class="text-gray-300">Rezoluție HD și 4K</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-125"></i>
                                <span class="text-gray-300">Fără watermark</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i
                                    class="text-green-400 transition-transform duration-300 ri-checkbox-circle-fill group-hover:scale-125"></i>
                                <span class="text-gray-300">Suport prioritar 24/7</span>
                            </li>
                        </ul>
                        <a href="#notify"
                            class="relative block w-full px-6 py-3 overflow-hidden text-lg font-medium text-center text-white transition-all duration-500 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 hover:shadow-xl hover:shadow-purple-500/30">
                            <span
                                class="absolute inset-0 w-full h-full bg-gradient-to-r from-purple-600/0 via-white/20 to-purple-600/0 transform translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></span>
                            <span class="relative">Anunță-mă când e gata</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <x-footer />

        <!-- Add Scripts for Animations -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize AOS animation library
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 100,
                });


            });
        </script>
        @livewireScripts
</body>

</html>
