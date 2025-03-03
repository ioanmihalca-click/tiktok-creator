<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Plată reușită') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h3 class="mb-2 text-2xl font-bold text-gray-900">Mulțumim pentru achiziție!</h3>
                    <p class="mb-6 text-gray-600">Plata a fost procesată cu succes și creditele au fost adăugate în
                        contul tău.</p>

                    <div class="flex flex-col items-center space-y-4">
                        <a href="{{ route('credits.index') }}"
                            class="inline-flex items-center px-4 py-2 font-semibold text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Vezi creditele disponibile
                        </a>

                        <a href="{{ route('tiktoks.create') }}"
                            class="inline-flex items-center px-4 py-2 font-semibold text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Creează un videoclip nou
                        </a>
                    </div>

                    <div class="mt-8 text-sm text-gray-500">
                        ID Tranzacție: {{ $session_id }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
