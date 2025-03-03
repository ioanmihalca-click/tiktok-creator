<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-xl font-semibold leading-tight text-white">
                {{ __('Pachete de videoclipuri') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 border rounded-2xl bg-white/5 backdrop-blur-sm border-white/10">
                <!-- Available Credits Section -->
                <div class="mb-8">
                    <h3 class="flex items-center gap-3 mb-4 text-lg font-medium text-gray-200">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Credite disponibile
                    </h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="p-4 border rounded-lg bg-blue-900/20 border-blue-500/20">
                            <p class="text-sm text-gray-400">Videoclipuri gratuite</p>
                            <p class="text-2xl font-bold text-white">
                                {{ $userCredit ? $userCredit->available_free_credits : 0 }}</p>
                            <p class="mt-1 text-xs text-gray-400">Include watermark</p>
                        </div>
                        <div class="p-4 border rounded-lg bg-green-900/20 border-green-500/20">
                            <p class="text-sm text-gray-400">Videoclipuri premium</p>
                            <p class="text-2xl font-bold text-white">
                                {{ $userCredit ? $userCredit->available_credits : 0 }}</p>
                            <p class="mt-1 text-xs text-gray-400">Fără watermark</p>
                        </div>
                    </div>
                </div>

                <!-- Packages Section -->
                <div class="pt-8 border-t border-white/10">
                    <h3 class="flex items-center gap-3 mb-6 text-lg font-medium text-gray-200">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                        Pachete disponibile
                    </h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($packages as $package)
                            <div
                                class="overflow-hidden transition-all duration-300 border rounded-xl bg-white/5 border-white/10 hover:border-purple-500/30">
                                <div
                                    class="p-4 border-b bg-gradient-to-r from-purple-900/30 to-blue-900/30 border-white/10">
                                    <h4 class="text-xl font-semibold text-gray-200">{{ $package->name }}</h4>
                                    <p class="mt-2 text-sm text-gray-400">{{ $package->description }}</p>
                                </div>
                                <div class="p-6">
                                    <div class="mb-6">
                                        <div
                                            class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
                                            {{ number_format($package->price / 100, 2) }} lei
                                        </div>
                                    </div>
                                    <a href="{{ route('credits.checkout', $package->id) }}"
                                        class="block w-full px-4 py-3 text-center text-white transition-all duration-300 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700">
                                        Achiziționează
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="pt-8 mt-8 border-t border-white/10">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-purple-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-400">
                                <strong class="text-white">Notă:</strong> Videoclipurile generate cu credite gratuite
                                vor include un logo watermark. Videoclipurile generate cu credite premium nu vor include
                                watermark și vor fi de calitate superioară.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('credits.history') }}"
                                    class="inline-flex items-center gap-2 text-sm text-purple-400 transition-colors duration-200 hover:text-purple-300">
                                    Vezi istoricul tranzacțiilor
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
