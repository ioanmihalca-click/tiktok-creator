<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-white">
            {{ __('Istoricul tranzacțiilor') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-white">Tranzacțiile tale</h3>
                        <a href="{{ route('credits.index') }}"
                            class="text-purple-400 transition-colors duration-200 hover:text-purple-300">
                            Înapoi la pachete
                        </a>
                    </div>

                    @if ($transactions->isEmpty())
                        <div class="py-8 text-center">
                            <p class="text-gray-400">Nu ai efectuat încă nicio tranzacție.</p>
                            <a href="{{ route('credits.index') }}"
                                class="inline-block mt-4 text-purple-400 transition-colors duration-200 hover:text-purple-300">
                                Achiziționează credite
                            </a>
                        </div>
                    @else
                        <!-- Tabel pentru Desktop -->
                        <div class="hidden overflow-x-auto md:block">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-300 uppercase">
                                            Data
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-300 uppercase">
                                            Tip
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-300 uppercase">
                                            Descriere
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-300 uppercase">
                                            Credite
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-300 whitespace-nowrap">
                                                {{ $transaction->created_at->format('d.m.Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($transaction->transaction_type == 'purchase')
                                                    <span
                                                        class="inline-flex px-2 text-xs font-semibold leading-5 text-green-400 rounded-full bg-green-900/50">
                                                        Achiziție
                                                    </span>
                                                @elseif($transaction->transaction_type == 'usage')
                                                    <span
                                                        class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-400 rounded-full bg-blue-900/50">
                                                        Utilizare
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex px-2 text-xs font-semibold leading-5 text-gray-300 bg-gray-700 rounded-full">
                                                        {{ $transaction->transaction_type }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-300">
                                                {{ $transaction->description }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                                <span
                                                    class="{{ $transaction->amount > 0 ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ $transaction->amount }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Carduri pentru Mobile -->
                        <div class="space-y-4 md:hidden">
                            @foreach ($transactions as $transaction)
                                <div class="p-4 bg-gray-700 rounded-lg">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="text-sm text-gray-300">
                                            {{ $transaction->created_at->format('d.m.Y H:i') }}
                                        </div>
                                        @if ($transaction->transaction_type == 'purchase')
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-green-400 rounded-full bg-green-900/50">
                                                Achiziție
                                            </span>
                                        @elseif($transaction->transaction_type == 'usage')
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-400 rounded-full bg-blue-900/50">
                                                Utilizare
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-gray-300 bg-gray-700 rounded-full">
                                                {{ $transaction->transaction_type }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mb-2 text-sm text-gray-300">
                                        {{ $transaction->description }}
                                    </div>

                                    <div class="text-right">
                                        <span
                                            class="{{ $transaction->amount > 0 ? 'text-green-400' : 'text-red-400' }} text-lg font-semibold">
                                            {{ $transaction->amount > 0 ? '+' : '' }}{{ $transaction->amount }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
