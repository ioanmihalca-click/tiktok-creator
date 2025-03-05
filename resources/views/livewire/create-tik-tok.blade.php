<div>

    <div class="min-h-screen text-white bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <div class="max-w-5xl p-6 mx-auto">
            <!-- Header -->
            <div class="mb-12 text-center">

                <h1
                    class="mb-3 text-4xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
                    TikTok Creator
                </h1>
                <p class="text-lg text-gray-400">Generează videoclipuri TikTok cu AI în câțiva pași simpli</p>
            </div>

            <!-- Notifications -->
            <div class="absolute inset-x-0 top-0 z-50 space-y-4">
                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-show="show"
                        class="max-w-lg p-4 mx-auto transition-all duration-300 transform rounded-lg bg-green-900/50 backdrop-blur-sm animate-fade-in-down">
                        <div class="flex justify-between">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p>{{ session('message') }}</p>
                                </div>
                            </div>
                            <svg @click="show = false" class="w-5 h-5 text-green-200 cursor-pointer hover:text-white"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div x-data="{ show: true }" x-show="show"
                        class="max-w-lg p-4 mx-auto transition-all duration-300 transform rounded-lg bg-red-900/50 backdrop-blur-sm animate-fade-in-down">
                        <div class="flex justify-between">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p>{{ session('error') }}</p>
                                </div>
                            </div>
                            <svg @click="show = false" class="w-5 h-5 text-red-200 cursor-pointer hover:text-white"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                @endif
            </div>


            <!-- Main Content Container -->
            <div class="relative">

                <!-- Category Selection Cards -->
                <div
                    class="p-8 transition-all duration-300 border rounded-2xl bg-white/5 backdrop-blur-sm border-white/10 hover:bg-white/[0.07]">
                    <div class="flex items-center justify-between mb-6">
                        <label class="text-lg font-medium text-gray-200">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                                Alege Categoria
                            </span>
                        </label>

                        <div class="text-sm text-gray-400">
                            @if ($categorySlug)
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-500/20">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $categorySlug }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3" x-data="{
                        selectedCategory: @entangle('categorySlug'),
                        setCategory(slug) {
                            this.selectedCategory = slug;
                            $wire.setCategory(slug);
                        }
                    }">
                        @foreach ($categories as $category)
                            <div
                                class="overflow-hidden transition-all duration-300 border rounded-xl bg-white/5 border-white/10 hover:border-purple-500/30">
                                <!-- Card Header -->
                                <div
                                    class="p-4 border-b bg-gradient-to-r from-purple-900/30 to-blue-900/30 border-white/10">
                                    <h3 class="flex items-center gap-3 text-lg font-semibold text-gray-200">
                                        {{ $category->name }}
                                    </h3>
                                    @if ($category->description)
                                        <p class="mt-1 text-sm text-gray-400">{{ $category->description }}</p>
                                    @endif
                                </div>

                                <!-- Card Content -->
                                <div class="p-4">
                                    <div class="space-y-2">
                                        @foreach ($category->children as $subCategory)
                                            <div x-data="{ open: false }" class="overflow-hidden rounded-lg">
                                                <button @click="open = !open"
                                                    class="flex items-center justify-between w-full p-3 text-left transition-all duration-200 rounded-lg bg-white/5 hover:bg-white/10 group">
                                                    <span
                                                        class="text-sm font-medium text-gray-300 group-hover:text-purple-400">
                                                        {{ $subCategory->name }}
                                                    </span>
                                                    @if ($subCategory->children->count() > 0)
                                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover:text-purple-400"
                                                            :class="{ 'rotate-180': open }" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    @endif
                                                </button>

                                                @if ($subCategory->children->count() > 0)
                                                    <div x-show="open"
                                                        x-transition:enter="transition ease-out duration-200"
                                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                                        x-transition:enter-end="opacity-100 translate-y-0"
                                                        class="mt-1 ml-3">
                                                        @foreach ($subCategory->children as $childCategory)
                                                            @if ($childCategory->children->count() > 0)
                                                                <div x-data="{ openChild: false }" class="mt-1">
                                                                    <button @click="openChild = !openChild"
                                                                        class="flex items-center justify-between w-full p-3 text-sm text-left text-gray-300 transition-all duration-200 rounded-lg bg-white/5 hover:bg-white/10 group">
                                                                        <span
                                                                            class="group-hover:text-purple-400">{{ $childCategory->name }}</span>
                                                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover:text-purple-400"
                                                                            :class="{ 'rotate-180': openChild }"
                                                                            fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                        </svg>
                                                                    </button>
                                                                    <div x-show="openChild"
                                                                        x-transition:enter="transition ease-out duration-200"
                                                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                                                        x-transition:enter-end="opacity-100 translate-y-0"
                                                                        class="pl-4 mt-1 space-y-1">
                                                                        @foreach ($childCategory->children as $grandChildCategory)
                                                                            <button
                                                                                @click="setCategory('{{ $grandChildCategory->slug }}')"
                                                                                class="w-full p-2 text-sm text-left text-gray-300 transition-all duration-200 rounded-lg hover:bg-white/5"
                                                                                :class="{ 'bg-purple-900/50 text-purple-400': selectedCategory === '{{ $grandChildCategory->slug }}', 'text-gray-300': selectedCategory !== '{{ $grandChildCategory->slug }}' }">
                                                                                {{ $grandChildCategory->name }}
                                                                            </button>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <button
                                                                    @click="setCategory('{{ $childCategory->slug }}')"
                                                                    class="w-full p-2 text-sm text-left text-gray-300 transition-all duration-200 rounded-lg hover:bg-white/5"
                                                                    :class="{ 'bg-purple-900/50 text-purple-400': selectedCategory === '{{ $childCategory->slug }}', 'text-gray-300': selectedCategory !== '{{ $childCategory->slug }}' }">
                                                                    {{ $childCategory->name }}
                                                                </button>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <button @click="setCategory('{{ $subCategory->slug }}')"
                                                        class="w-full p-3 text-sm text-left text-gray-300 transition-all duration-200 hover:bg-white/5"
                                                        :class="{ 'bg-purple-900/50 text-purple-400': selectedCategory === '{{ $subCategory->slug }}', 'text-gray-300': selectedCategory !== '{{ $subCategory->slug }}' }">
                                                        {{ $subCategory->name }}
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Credit Information -->
                <div class="p-6 mt-8 border rounded-xl bg-white/5 backdrop-blur-sm border-white/10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-200">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Credite Disponibile
                            </span>
                        </h3>
                        <a href="{{ route('credits.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">
                            Achiziționează credite →
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-4">
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

                        @if (!$hasCredits)
                            <div class="flex-1 p-4 border rounded-lg bg-red-900/20 border-red-500/20">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-red-400">Nu ai credite disponibile</p>
                                        <p class="mt-1 text-sm text-gray-400">Pentru a genera un videoclip, trebuie să
                                            achiziționezi un pachet de credite sau să aștepți până când primești credite
                                            gratuite.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif ($creditType === 'free')
                            <div class="flex-1 p-4 border rounded-lg bg-blue-900/20 border-blue-500/20">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-blue-400">Vei folosi un credit gratuit</p>
                                        <p class="mt-1 text-sm text-gray-400">Videoclipul generat va include un logo
                                            watermark. Pentru videoclipuri fără watermark, achiziționează un pachet
                                            premium.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif ($creditType === 'paid')
                            <div class="flex-1 p-4 border rounded-lg bg-green-900/20 border-green-500/20">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-green-400">Vei folosi un credit premium</p>
                                        <p class="mt-1 text-sm text-gray-400">Videoclipul generat va fi de calitate
                                            superioară, fără watermark.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Voci narare -->
                <div class="p-6 mt-8 border rounded-xl bg-white/5 backdrop-blur-sm border-white/10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-200">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                </svg>
                                Opțiuni de narare
                            </span>
                        </h3>
                    </div>

                    <!-- Voci gratuite -->
                    <div class="mb-6">
                        <h4 class="mb-3 text-sm font-medium text-gray-400">Voci gratuite</h4>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @foreach ($availableVoices['free'] as $voice)
                                <div wire:click="selectVoice('{{ $voice['voice_id'] }}')"
                                    class="p-4 transition-all duration-200 border rounded-lg cursor-pointer hover:bg-white/10 
                                    {{ $selectedVoiceId === $voice['voice_id'] ? 'bg-purple-900/30 border-purple-500' : 'bg-white/5 border-white/10' }}">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="text-base font-medium text-white">{{ $voice['name'] }}</h5>
                                        @if ($selectedVoiceId === $voice['voice_id'])
                                            <span
                                                class="flex items-center px-2 py-1 text-xs font-medium text-purple-300 rounded-full bg-purple-500/30">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Selectat
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-400">
                                        @if ($voice['voice_id'] === 'S98OhkhaxeAKHEbhoLi7')
                                            Voce standard masculină de vârstă mijlocie, caldă și sofisticată, perfectă
                                            pentru narațiuni.
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Voci premium -->
                    @if (!empty($availableVoices['premium']))
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-purple-400">Voci premium</h4>
                                @if ($creditType !== 'paid')
                                    <span
                                        class="px-2 py-1 text-xs font-medium text-yellow-400 rounded-full bg-yellow-900/30">
                                        Necesită credit premium
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                @foreach ($availableVoices['premium'] as $voice)
                                    <div
                                        @if ($creditType === 'paid') wire:click="selectVoice('{{ $voice['voice_id'] }}')"
                                            class="p-4 transition-all duration-200 border rounded-lg cursor-pointer hover:bg-white/10 
                                            {{ $selectedVoiceId === $voice['voice_id'] ? 'bg-purple-900/30 border-purple-500' : 'bg-white/5 border-white/10' }}"
                                        @else
                                            class="p-4 border rounded-lg bg-white/5 border-white/10 opacity-60" @endif>
                                        <div class="flex items-center justify-between mb-2">
                                            <h5 class="text-base font-medium text-white">{{ $voice['name'] }}</h5>
                                            @if ($selectedVoiceId === $voice['voice_id'])
                                                <span
                                                    class="flex items-center px-2 py-1 text-xs font-medium text-purple-300 rounded-full bg-purple-500/30">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Selectat
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-400">
                                            @switch($voice['voice_id'])
                                                @case('OlBp4oyr3FBAGEAtJOnU')
                                                    Voce calmă și profundă, perfectă pentru narațiuni, audiobook-uri și
                                                    informații generale.
                                                @break

                                                @case('gbLy9ep70G3JW53cTzFC')
                                                    Voce feminină de vârstă mijlocie, narând pe un ton conversațional.
                                                @break

                                                @case('4zwat5xS9O6SetLUEbxv')
                                                    Voce feminină tânără cu un ton încrezător, perfectă pentru podcast-uri și
                                                    reclame.
                                                @break

                                                @case('KxGkxCicfy28RgQTZuHk')
                                                    Voce profesională care aduce viață reclamelor și conținutului pentru social
                                                    media.
                                                @break

                                                @default
                                                    Voce premium de înaltă calitate.
                                            @endswitch
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            @if ($creditType !== 'paid')
                                <div class="p-3 mt-4 border rounded-lg bg-yellow-900/20 border-yellow-500/20">
                                    <p class="flex items-start gap-2 text-sm text-yellow-400">
                                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Pentru a accesa voci premium, achiziționează un pachet premium.</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <p class="mt-2 text-sm text-yellow-400">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @if ($creditType === 'paid')
                            Folosești voci premium, beneficiu inclus în pachetele plătite.
                        @else
                            Pentru a accesa mai multe voci, achiziționează un pachet premium.
                        @endif
                    </p>


                </div>

                <!-- Generate Button & Loading State -->
                <div class="relative mt-8">
                    <button type="button" wire:click="generate"
                        class="relative w-full px-8 py-4 text-lg font-medium text-white transition-all duration-300 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                        wire:loading.attr="disabled" wire:target="generate" wire:loading.class="opacity-75"
                        @if (!$hasCredits) disabled @endif
                        @if (!$hasCredits) class="opacity-50 cursor-not-allowed" @endif>
                        <span wire:loading.remove wire:target="generate"
                            class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Generează TikTok
                        </span>
                        <span wire:loading wire:target="generate" class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{-- Procesare... --}}
                        </span>
                    </button>

                    <!-- Loading Message -->
                    <div wire:loading.remove wire:target="generate">
                        <!-- Preview Section -->
                        @if ($isProcessing || $videoUrl)
                            <div class="mt-12 space-y-8">
                                <!-- Processing State -->
                                @if ($isProcessing)
                                    <div wire:poll.5s="checkStatus">
                                        <div class="p-6 bg-white/5 backdrop-blur-sm rounded-xl">
                                            <h2 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-300">
                                                <svg class="w-5 h-5 text-purple-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Procesare Video
                                            </h2>
                                            <div class="p-4 rounded-lg bg-white/5">
                                                <div class="flex flex-col gap-4">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex items-center gap-2">
                                                                <div
                                                                    class="w-2 h-2 bg-blue-500 rounded-full animate-pulse">
                                                                </div>
                                                                <div
                                                                    class="w-2 h-2 bg-purple-500 rounded-full animate-pulse [animation-delay:0.2s]">
                                                                </div>
                                                                <div
                                                                    class="w-2 h-2 bg-pink-500 rounded-full animate-pulse [animation-delay:0.4s]">
                                                                </div>
                                                            </div>
                                                            <span
                                                                class="font-medium text-purple-400">{{ $currentStep }}</span>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="relative w-full h-2 overflow-hidden bg-gray-700 rounded-full">
                                                        <div
                                                            class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-progress">
                                                        </div>
                                                    </div>
                                                    <p class="mt-2 text-sm text-center text-gray-400">
                                                        Procesarea videoclipului durează câteva minute. Vă mulțumim
                                                        pentru răbdare!
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Final Video -->
                                @if ($videoUrl)
                                    <div class="max-w-lg p-6 mx-auto bg-white/5 backdrop-blur-sm rounded-xl">
                                        <div class="mb-6 text-center">
                                            <h2 class="mb-2 text-xl font-bold text-gray-200">
                                                {{ $script['title'] ?? 'Video Generat' }}</h2>

                                            <!-- Hashtaguri -->
                                            @if (isset($script['hashtags']) && is_array($script['hashtags']))
                                                <div class="flex flex-wrap justify-center gap-2 mb-3">
                                                    @foreach ($script['hashtags'] as $hashtag)
                                                        <span
                                                            class="px-3 py-1 text-sm text-blue-400 rounded-full bg-blue-500/10">
                                                            {{ $hashtag }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif


                                        </div>

                                        <div class="overflow-hidden rounded-lg aspect-w-9 aspect-h-16">
                                            <video controls class="object-cover w-full">
                                                <source src="{{ $videoUrl }}" type="video/mp4">
                                                Browser-ul tău nu suportă redarea video.
                                            </video>
                                        </div>

                                        <div
                                            class="inline-flex items-center gap-3 px-4 py-3 mt-3 text-gray-400 transition-all duration-200 border rounded-lg border-white/10 bg-white/5 backdrop-blur-sm">
                                            <svg class="text-purple-400 w-7 h-7" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm">
                                                Pentru a descărca videoclipul, apasă pe cele trei puncte din player și
                                                selectează "Download"
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Initial Processing Modal -->
                    <div x-data="{ show: @entangle('showInitialProcessingModal') }" x-show="show" x-cloak
                        class="fixed inset-0 z-[100] flex items-center justify-center">
                        <!-- Overlay cu blur -->
                        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

                        <!-- Conținutul modalului - am corectat poziționarea -->
                        <div class="relative z-[101] w-full max-w-md mx-auto">
                            <div
                                class="relative p-6 border shadow-2xl bg-gray-900/95 border-white/10 rounded-xl backdrop-blur-sm">
                                <h2 class="flex items-center gap-2 mb-6 text-xl font-bold text-gray-200">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Inițializare Procesare
                                </h2>

                                <div class="p-4 rounded-lg bg-white/5">
                                    <div class="flex flex-col gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                                <div
                                                    class="w-2 h-2 bg-purple-500 rounded-full animate-pulse [animation-delay:0.2s]">
                                                </div>
                                                <div
                                                    class="w-2 h-2 bg-pink-500 rounded-full animate-pulse [animation-delay:0.4s]">
                                                </div>
                                            </div>
                                            <span class="font-medium text-purple-400">Se inițializează procesul de
                                                generare...</span>
                                        </div>
                                        <div class="relative w-full h-2 overflow-hidden bg-gray-700 rounded-full">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-progress">
                                            </div>
                                        </div>
                                        <div class="mt-6 space-y-3">
                                            <div class="flex items-center gap-2 text-blue-400">
                                                <span class="text-lg">✨</span>
                                                <span>Se compune scenariul pentru TikTok-ul tău</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-purple-400">
                                                <span class="text-lg">🎙️</span>
                                                <span>Se pregătește nararea audio</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-pink-400">
                                                <span class="text-lg">🎨</span>
                                                <span>Se creează elementele vizuale</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out;
        }

        @keyframes progress {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .animate-progress {
            animation: progress 2s linear infinite;
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('videoReady', () => {
                //Livewire.dispatch('refresh'); // Nu mai este nevoie, se face auto prin poll
            });

            Livewire.on('processingStarted', () => {
                window.scrollTo({
                    top: document.querySelector('.min-h-screen').scrollHeight,
                    behavior: 'smooth'
                });

                // Adăugăm un timer care va închide modalul inițial după 30 de secunde
                // Poți ajusta valoarea (30000) pentru a crește sau reduce durata de afișare
                setTimeout(() => {
                    @this.finishInitialProcessing();
                }, 20000); // 20 secunde
            });
        });
    </script>
</div>
