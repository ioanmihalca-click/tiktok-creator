<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-8 border rounded-2xl bg-white/5 backdrop-blur-sm border-white/10 hover:bg-white/[0.07]">
                <!-- Stats Section -->
                <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
                    <!-- Videos Created -->
                    <div class="p-6 border rounded-xl bg-white/5 border-white/10">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-lg bg-purple-500/20">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Videoclipuri Create</p>
                                <p class="text-2xl font-bold text-white">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Available Credits -->
                    <a href="{{ route('credits.index') }}" class="block">
                        <div
                            class="p-6 transition-colors border rounded-xl bg-white/5 border-white/10 hover:bg-white/10">
                            <div class="flex items-center gap-4">
                                <div class="p-3 rounded-lg bg-blue-500/20">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Credite Disponibile</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-2xl font-bold text-white">
                                            {{ Auth::user()->userCredit ? Auth::user()->userCredit->total_available_credits : 0 }}
                                        </p>
                                        <span
                                            class="text-xs text-gray-400">({{ Auth::user()->userCredit ? Auth::user()->userCredit->available_free_credits : 0 }}
                                            gratuite)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Subscription Status -->
                    <div class="p-6 border rounded-xl bg-white/5 border-white/10">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-lg bg-pink-500/20">
                                <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Plan Curent</p>
                                <p class="text-2xl font-bold text-white">Free</p>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</x-app-layout>
