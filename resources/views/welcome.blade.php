<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TikTok Creator AI</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased text-white bg-gradient-to-br from-gray-900 to-gray-800">
    <div class="min-h-screen">
        <!-- Hero Section with Logo and Newsletter -->
        <div class="container px-4 py-16 mx-auto text-center">
            <div class="max-w-xl mx-auto mb-12">
                <img src="{{ asset('assets/logo-transparent.webp') }}" alt="TikTok Maker AI Logo"
                    class="h-32 mx-auto mb-8">

                <h2
                    class="mb-6 text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
                    Aplicația va fi disponibilă în curând!
                </h2>

                <div class="mb-4 space-y-6 text-center">
                    <div class="flex items-center justify-center gap-3 text-xl text-gray-200">

                        <p>Creează filmulețe virale pentru TikTok instant. Aplicatia noastra integreaza cele mai bune
                            modele AI. Totul în mai puțin de 5 minute.</p>
                    </div>



                </div>

                <!-- Newsletter Form -->
                <div class="max-w-md p-6 mx-auto bg-white/5 backdrop-blur-sm rounded-xl">
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-400 rounded-lg bg-green-900/50">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="p-4 mb-4 text-sm text-red-400 rounded-lg bg-red-900/50">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                    <form action="{{ route('subscribe') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="sr-only">Email address</label>
                            <input type="email" name="email" id="email" required
                                class="w-full px-4 py-3 text-gray-900 placeholder-gray-500 bg-white rounded-lg"
                                placeholder="Introdu adresa ta de email">
                        </div>
                        <button type="submit"
                            class="w-full px-6 py-3 text-white transition-colors duration-200 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700">
                            Anunță-mă când e gata!
                        </button>
                    </form>
                </div>
            </div>

            <!-- Featured Videos Section -->
            {{-- <div class="mt-24">
                <h3 class="mb-12 text-2xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
                     Creații
                </h3>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Video Card Example (Repeat as needed) -->
                    <div class="overflow-hidden bg-white/5 rounded-xl backdrop-blur-sm">
                        <div class="aspect-[9/16]">
                            <!-- Replace src with your actual video URL -->
                            <video 
                                class="object-cover w-full h-full"
                                controls
                                >
                                <source src="{{ asset('assets/featuredVideos/featuredVideo1.mp4') }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="p-4">
                            <h4 class="mb-2 text-lg font-semibold">Titlu Video</h4>
                            <p class="text-sm text-gray-400">Descriere scurtă a videoclipului...</p>
                        </div>
                    </div>

                    <!-- Add more video cards here -->
                </div>
            </div>
        </div> --}}

            <!-- Footer -->
            <footer class="py-8 mt-16 text-sm text-center text-gray-400 border-t border-gray-800">
                <p>&copy; {{ date('Y') }} TikTok Creator. Toate drepturile rezervate.</p>
                <p class="mt-2">
                    Aplicație dezvoltată de
                    <a href="https://clickstudios-digital.com" target="_blank" rel="noopener noreferrer"
                        class="text-purple-400 transition-colors duration-200 hover:text-purple-300">
                        Click Studios Digital
                    </a>
                </p>
            </footer>
        </div>
</body>

</html>
