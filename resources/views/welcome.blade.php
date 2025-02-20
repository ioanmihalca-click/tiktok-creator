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

<body class="antialiased bg-[#0A0A0F] text-white min-h-screen">
    <!-- Decorative gradient effects -->
    <div
        class="fixed inset-0 -z-10 h-screen w-full bg-[#0A0A0F] bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.3),rgba(255,255,255,0))]">
    </div>

    <div class="relative min-h-screen">
        <!-- Hero Section -->
        <div class="container max-w-6xl px-4 py-12 mx-auto">
            <div class="text-center">
                <img src="{{ asset('assets/logo-transparent.webp') }}" alt="TikTok Maker AI Logo"
                    class="h-24 mx-auto mb-12 transition-transform duration-300 transform hover:scale-105">

                <!-- Main Content Container -->
                <div class="max-w-2xl mx-auto space-y-8">
                    <!-- Beta Badge -->
                    <div
                        class="inline-flex items-center px-4 py-2 space-x-2 text-sm font-medium text-yellow-200 rounded-full bg-yellow-900/20">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                        </svg>
                        <span>Versiune Beta</span>
                    </div>

                    <!-- Beta Message -->
                    <div
                        class="p-6 border rounded-2xl bg-gradient-to-br from-purple-900/30 to-blue-900/30 backdrop-blur-sm border-white/10">
                        <h1
                            class="mb-6 text-4xl font-bold text-transparent md:text-5xl bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 bg-clip-text">
                            Creează filmulețe virale pentru TikTok
                        </h1>
                        <p class="max-w-2xl mx-auto mb-8 text-xl text-gray-300">
                            Transformă-ți ideile în conținut viral folosind puterea inteligenței artificiale.
                            Totul în mai puțin de 5 minute.
                        </p>

                        <p class="text-gray-300">
                            Aplicația TikTok-Creator este în versiune beta si se poate testa gratuit. Filmulețele
                            generate vor avea logoul
                            TikTok-Creator și Shotstack. Așteptăm sugestiile voastre de îmbunătățire!
                        </p>

                    </div>


                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="inline-flex items-center justify-center w-full px-8 py-4 text-lg font-medium text-white transition-all duration-200 transform border border-transparent sm:w-auto rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 hover:scale-105">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Creează TikTok
                            </span>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center w-full px-8 py-4 text-lg font-medium text-white transition-all duration-200 transform border border-transparent sm:w-auto rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 hover:scale-105">
                            Începe Gratuit
                        </a>
                        <a href="#how-it-works"
                            class="inline-flex items-center justify-center w-full px-8 py-4 text-lg font-medium text-gray-300 transition-all duration-200 border sm:w-auto rounded-xl border-white/10 hover:bg-white/5">
                            Cum Funcționează
                        </a>
                    @endauth
                </div>
            </div>
        </div>


        <!-- Process Steps -->
        <section id="how-it-works" class="py-24 bg-gradient-to-b from-transparent to-black/30">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold md:text-4xl">Procesul de Creare</h2>
                    <p class="text-gray-400">De la idee la TikTok viral în 4 pași simpli</p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Step 1: Script -->
                    <div class="relative p-6 border bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm">
                        <div
                            class="absolute flex items-center justify-center w-8 h-8 text-lg font-bold bg-purple-600 rounded-full -top-4 left-4">
                            1</div>
                        <div class="pt-4">
                            <h3 class="mb-4 text-xl font-semibold text-purple-400">Generare Script</h3>
                            <p class="text-gray-400">AI-ul nostru analizează tendințele și generează un
                                script optimizat
                                pentru engagement.</p>
                        </div>
                    </div>

                    <!-- Step 2: Image -->
                    <div class="relative p-6 border bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm">
                        <div
                            class="absolute flex items-center justify-center w-8 h-8 text-lg font-bold bg-blue-600 rounded-full -top-4 left-4">
                            2</div>
                        <div class="pt-4">
                            <h3 class="mb-4 text-xl font-semibold text-blue-400">Generare Imagini</h3>
                            <p class="text-gray-400">Creăm imagini atractive și relevante pentru conținutul
                                tău folosind AI.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3: Audio -->
                    <div class="relative p-6 border bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm">
                        <div
                            class="absolute flex items-center justify-center w-8 h-8 text-lg font-bold bg-pink-600 rounded-full -top-4 left-4">
                            3</div>
                        <div class="pt-4">
                            <h3 class="mb-4 text-xl font-semibold text-pink-400">Narare Audio</h3>
                            <p class="text-gray-400">Transformăm scriptul în narare audio naturală și
                                captivantă.</p>
                        </div>
                    </div>

                    <!-- Step 4: Video -->
                    <div class="relative p-6 border bg-white/5 rounded-2xl border-white/10 backdrop-blur-sm">
                        <div
                            class="absolute flex items-center justify-center w-8 h-8 text-lg font-bold bg-green-600 rounded-full -top-4 left-4">
                            4</div>
                        <div class="pt-4">
                            <h3 class="mb-4 text-xl font-semibold text-green-400">Asamblare Video</h3>
                            <p class="text-gray-400">Combinăm toate elementele într-un TikTok profesional
                                gata de postare.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-24">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold md:text-4xl">Planuri Simple</h2>
                    <p class="text-gray-400">Alege planul perfect pentru nevoile tale</p>
                </div>

                <div class="grid max-w-4xl grid-cols-1 gap-8 mx-auto md:grid-cols-2">
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
                        class="relative p-8 overflow-hidden border bg-gradient-to-br from-purple-900/30 to-blue-900/30 rounded-2xl border-purple-500/50 backdrop-blur-sm">
                        <!-- Popular Badge -->
                        <div
                            class="absolute top-0 right-0 px-4 py-1 text-sm font-medium text-white bg-purple-500 rounded-bl-lg">
                            Popular
                        </div>

                        <h3 class="mb-4 text-2xl font-bold">Premium (in curand)</h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">49 RON</span>
                            <span class="text-gray-400">/lună</span>
                        </div>
                        <ul class="mb-8 space-y-4">
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Videoclipuri nelimitate</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Mai multe voci de narare</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Postare automata pe TikTok</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Rezoluție HD</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fără watermark</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Suport prioritar</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="block w-full px-6 py-3 text-lg font-medium text-center text-white transition-all duration-200 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700">
                            Încearcă Premium
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <x-footer />
</body>

</html>
