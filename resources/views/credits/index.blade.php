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
            <div class="p-6 bg-gray-800 border border-gray-700 rounded-lg shadow-sm">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-white">Credite disponibile</h3>
                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                        <div class="p-4 bg-gray-700 rounded-lg">
                            <p class="text-gray-400">Videoclipuri gratuite</p>
                            <p class="text-2xl font-bold text-white">
                                {{ $userCredit ? $userCredit->available_free_credits : 0 }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-700 rounded-lg">
                            <p class="text-gray-400">Videoclipuri premium</p>
                            <p class="text-2xl font-bold text-white">
                                {{ $userCredit ? $userCredit->available_credits : 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-700">
                    <h3 class="mb-4 text-lg font-medium text-white">Achiziționează pachete de videoclipuri</h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($packages as $package)
                            <div class="flex flex-col p-6 bg-gray-700 border border-gray-600 rounded-lg">
                                <h4 class="text-xl font-semibold text-white">{{ $package->name }}</h4>
                                <p class="mt-2 text-gray-400">{{ $package->description }}</p>
                                <div class="mt-4 text-2xl font-bold text-purple-400">
                                    {{ number_format($package->price / 100, 2) }} lei
                                </div>
                                <div class="pt-4 mt-auto">
                                    <a href="{{ route('credits.checkout', $package->id) }}"
                                        class="inline-flex items-center justify-center w-full px-4 py-2 font-semibold text-white transition-colors duration-200 bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Achiziționează
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-6 mt-8 border-t border-gray-700">
                    <p class="text-sm text-gray-400">
                        <strong class="text-white">Notă:</strong> Videoclipurile generate cu credite gratuite vor
                        include un logo
                        watermark. Videoclipurile generate cu credite premium nu vor include watermark și vor fi de
                        calitate
                        superioară.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('credits.history') }}"
                            class="text-purple-400 transition-colors duration-200 hover:text-purple-300">
                            Vezi istoricul tranzacțiilor →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
