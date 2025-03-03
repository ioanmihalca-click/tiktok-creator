<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-white">
            {{ __('Plată anulată') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-sm">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-yellow-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <h3 class="mb-2 text-2xl font-bold text-white">Plată anulată</h3>
                    <p class="mb-6 text-gray-400">Procesul de plată a fost anulat. Nu ți-a fost debitată nicio sumă.</p>

                    <div class="flex flex-col items-center space-y-4">
                        <a href="{{ route('credits.index') }}"
                            class="inline-flex items-center px-4 py-2 font-semibold text-white transition-colors duration-200 bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Înapoi la pachete
                        </a>

                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 font-semibold text-gray-300 transition-colors duration-200 border border-gray-600 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Înapoi la dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
