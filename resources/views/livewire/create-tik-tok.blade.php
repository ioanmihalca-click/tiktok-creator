<div class="min-h-screen text-white bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-5xl p-6 mx-auto">
        <!-- Header -->
        <div class="mb-12 text-center">
            <div class="inline-flex items-center justify-center p-2 mb-4 rounded-full bg-white/5 backdrop-blur-sm">
                <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="mb-3 text-4xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
                TikTok Creator
            </h1>
            <p class="text-lg text-gray-400">Generează videoclipuri TikTok cu AI în câțiva pași simpli</p>
        </div>

        <!-- Main Content Container -->
        <div class="relative">
            <!-- Notifications -->
            <div class="absolute inset-x-0 top-0 z-50 space-y-4">
                @if (session()->has('message'))
                    <div class="max-w-lg p-4 mx-auto transition-all duration-300 transform rounded-lg bg-green-900/50 backdrop-blur-sm animate-fade-in-down">
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
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="max-w-lg p-4 mx-auto transition-all duration-300 transform rounded-lg bg-red-900/50 backdrop-blur-sm animate-fade-in-down">
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
                    </div>
                @endif
            </div>

            <!-- Category Selection Cards -->
            <div class="p-8 transition-all duration-300 border rounded-2xl bg-white/5 backdrop-blur-sm border-white/10 hover:bg-white/[0.07]">
                <div class="flex items-center justify-between mb-6">
                    <label class="text-lg font-medium text-gray-200">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Alege Categoria
                        </span>
                    </label>
                    
                    <div class="text-sm text-gray-400">
                        @if($selectedVideoType)
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-500/20">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $selectedVideoType['name'] }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3" 
                    x-data="{ 
                        selectedCategory: @entangle('categorySlug'),
                        setCategory(slug) {
                            this.selectedCategory = slug;
                            $wire.setCategory(slug);
                        }
                    }">
                    @foreach ($categories as $mainSlug => $mainCategory)

                        @if (isset($mainCategory['name']) && isset($mainCategory['subcategories']))
                            <div class="{{ $mainSlug === 'meserii' ? 'lg:col-span-3 order-last' : '' }}">
                                <!-- Main Category Card -->
                                <div class="overflow-hidden transition-all duration-300 border rounded-xl bg-white/5 border-white/10 hover:border-purple-500/30">
                                    <!-- Card Header -->
                                    <div class="p-4 border-b bg-gradient-to-r from-purple-900/30 to-blue-900/30 border-white/10">
                                        <h3 class="flex items-center gap-3 text-lg font-semibold text-gray-200">
                                            @if(isset($mainCategory['icon']))
                                                <span class="p-1 rounded-lg bg-white/10">
                                                    {!! $mainCategory['icon'] !!}
                                                </span>
                                            @endif
                                            {{ $mainCategory['name'] }}
                                        </h3>
                                    </div>

                                    <!-- Card Content -->
                                    <div class="p-4">
                                        @if ($mainSlug === 'meserii')
                                            <div x-data="{ open: false }">
                                                <!-- Main dropdown button -->
                                                <button @click="open = !open"
                                                    class="flex items-center justify-between w-full p-3 text-left transition-all duration-200 rounded-lg bg-white/5 hover:bg-white/10 group">
                                                    <span class="text-sm font-medium text-gray-300 group-hover:text-purple-400">
                                                        Vezi toate meseriile
                                                    </span>
                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover:text-purple-400"
                                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <!-- Dropdown content -->
                                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                                    x-transition:enter-end="opacity-100 translate-y-0" class="mt-2">
                                                    <div class="grid grid-cols-2 gap-1 p-2 rounded-lg bg-white/5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                                                        @foreach ($mainCategory['subcategories'] as $subSlug => $subCategory)
                                                            <button 
                                                                @click="setCategory('{{ $subSlug }}')"
                                                                class="p-2 text-sm font-medium text-left text-gray-300 transition-all duration-200 rounded-lg hover:bg-white/10"
                                                                :class="{ 'bg-purple-900/50 text-purple-400': selectedCategory === '{{ $subSlug }}', 'text-gray-300': selectedCategory !== '{{ $subSlug }}' }">
                                                                {{ $subCategory['name'] }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="space-y-2">
                                                @foreach ($mainCategory['subcategories'] as $subSlug => $subCategory)
                                                    @if (isset($subCategory['name']))
                                                        <div x-data="{ open: false }" class="overflow-hidden rounded-lg">
                                                            <button @click="open = !open"
                                                                class="flex items-center justify-between w-full p-3 text-left transition-all duration-200 rounded-lg bg-white/5 hover:bg-white/10 group">
                                                                <span
                                                                    class="text-sm font-medium text-gray-300 group-hover:text-purple-400">
                                                                    {{ $subCategory['name'] }}
                                                                </span>
                                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover:text-purple-400"
                                                                    :class="{ 'rotate-180': open }" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            </button>

                                                            @if (isset($subCategory['subcategories']) && !empty($subCategory['subcategories']))
                                                                <div x-show="open"
                                                                    x-transition:enter="transition ease-out duration-200"
                                                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                                                    x-transition:enter-end="opacity-100 translate-y-0"
                                                                    class="mt-1">
                                                                    @foreach ($subCategory['subcategories'] as $subSubSlug => $subSubCat)
                                                                        <button
                                                                            @click="setCategory('{{ $subSubSlug }}')"
                                                                            class="w-full p-3 text-sm text-left text-gray-300 transition-all duration-200 hover:bg-white/5"
                                                                            :class="{ 'bg-purple-900/50 text-purple-400': selectedCategory === '{{ $subSubSlug }}', 'text-gray-300': selectedCategory !== '{{ $subSubSlug }}' }">
                                                                            {{ $subSubCat['name'] }}
                                                                        </button>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <button
                                                                    @click="setCategory('{{ $subSlug }}')"
                                                                    class="w-full p-3 text-sm text-left text-gray-300 transition-all duration-200 hover:bg-white/5"
                                                                    :class="{ 'bg-purple-900/50 text-purple-400': selectedCategory === '{{ $subSlug }}', 'text-gray-300': selectedCategory !== '{{ $subSlug }}' }">
                                                                    {{ $subCategory['name'] }}
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Video Type Information (if selected) -->
            @if($selectedVideoType)
                <div class="p-6 mt-6 transition-all duration-300 border rounded-xl bg-gradient-to-r from-purple-900/30 to-blue-900/30 border-white/10">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h3 class="mb-2 text-xl font-semibold text-gray-200">{{ $selectedVideoType['name'] }}</h3>
                            <p class="text-gray-400">{{ $selectedVideoType['description'] }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg bg-white/5">
                                <span class="block mb-1 text-sm font-medium text-purple-400">Format</span>
                                <span class="text-gray-300">{{ $selectedVideoType['script_type'] }}</span>
                            </div>
                            <div class="p-4 rounded-lg bg-white/5">
                                <span class="block mb-1 text-sm font-medium text-purple-400">Stil</span>
                                <span class="text-gray-300">{{ $selectedVideoType['image_type'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Generate Button & Loading State -->
            <div class="relative mt-8">
                <button type="button" wire:click="generate" 
                    class="relative w-full px-8 py-4 text-lg font-medium text-white transition-all duration-300 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                    wire:loading.attr="disabled" 
                    wire:target="generate" 
                    wire:loading.class="opacity-75">
                    <span wire:loading.remove wire:target="generate" class="flex items-center justify-center gap-2">
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
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{-- Se procesează... --}}
                    </span>
                </button>

                <!-- Loading Message -->
                <div wire:loading wire:target="generate"
                    class="max-w-2xl p-4 mx-auto mt-4 rounded-lg bg-white/5 backdrop-blur-sm">
                    <div class="flex flex-col items-center justify-center p-4 text-center">
                        <p class="text-sm text-gray-300">
                            Procesul de creație este în desfășurare. În acest moment:
                        </p>
                        <div class="mt-2 space-y-2">
                            <div class="text-blue-400">✨ Se compune scenariul pentru TikTok-ul tău</div>
                            <div class="text-purple-400">🎙️ Se pregătește nararea audio</div>
                            <div class="text-pink-400">🎨 Se creează elementele vizuale</div>
                        </div>
                        <p class="mt-3 text-sm text-gray-400">
                            Vă mulțumim pentru răbdare! Rezultatul va merita așteptarea.
                        </p>
                    </div>
                </div>

                <!-- Preview Section -->
                @if ($script || $audioUrl || $imageUrl || $videoUrl || $isProcessing)
                    <div class="mt-12 space-y-8">
                        <!-- Script Preview -->
                        @if ($script)
                            <div x-data="{ open: false }" class="p-6 bg-white/5 backdrop-blur-sm rounded-xl">
                                <h2 @click="open = !open"
                                    class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-300 cursor-pointer">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Script Generat
                                    <svg class="w-4 h-4 ml-auto text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </h2>
                                <div x-show="open" x-transition class="space-y-4">
                                    @foreach ($script['scenes'] as $scene)
                                        <div
                                            class="p-4 transition-all duration-200 border border-gray-100 rounded-lg hover:border-purple-100 hover:bg-purple-50">
                                            <div class="flex items-center justify-between mb-2">
                                                <span
                                                    class="px-2 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full">
                                                    Scena {{ $loop->iteration }}
                                                </span>
                                            </div>
                                            <p class="mb-2 text-gray-800">{{ $scene['text'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $scene['narration'] }}</p>
                                        </div>
                                    @endforeach

                                    @if (isset($script['hashtags']))
                                        <div class="flex flex-wrap gap-2 mt-4">
                                            @foreach ($script['hashtags'] as $hashtag)
                                                <span class="px-3 py-1 text-sm text-blue-600 bg-blue-100 rounded-full">
                                                    {{ $hashtag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Audio Preview -->
                        @if ($audioUrl)
                            <div x-data="{ open: false }" class="p-6 bg-white/5 backdrop-blur-sm rounded-xl">
                                <h2 @click="open = !open"
                                    class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-300 cursor-pointer">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 001.414 1.414m2.828-9.9a9 9 0 012.828-2.828" />
                                    </svg>
                                    Narare Generată
                                    <svg class="w-4 h-4 ml-auto text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </h2>
                                <div x-show="open" x-transition class="p-4 rounded-lg bg-gray-50">
                                    <audio controls class="w-full">
                                        <source src="{{ $audioUrl }}" type="audio/mpeg">
                                        Browser-ul tău nu suportă redarea audio.
                                    </audio>
                                </div>
                            </div>
                        @endif

                        <!-- Image Preview -->
                        @if ($imageUrl)
                            <div x-data="{ open: false }" class="p-6 bg-white/5 backdrop-blur-sm rounded-xl">
                                <h2 @click="open = !open"
                                    class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-300 cursor-pointer">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Imagine Generată
                                    <svg class="w-4 h-4 ml-auto text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </h2>
                                <div x-show="open" x-transition class="overflow-hidden rounded-lg">
                                    <img src="{{ $imageUrl }}" alt="Imagine de fundal generată"
                                        class="object-cover w-full transition-transform duration-300 hover:scale-105">
                                </div>
                            </div>
                        @endif

                        <!-- Processing State (when isProcessing is true) -->
                        @if ($isProcessing)
                            <div wire:poll.5s="checkStatus">
                                <div class="p-6 bg-white/5 backdrop-blur-sm rounded-xl">
                                    <h2 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-300">
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Procesare Video
                                    </h2>
                                    <div class="p-4 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50">
                                        <div class="flex flex-col gap-4">
                                            <div class="flex items-center justify-between">
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
                                                    <span class="font-medium text-purple-400">Videoclipul tău este în curs
                                                        de
                                                        procesare...</span>
                                                </div>
                                                <button wire:click="checkStatus" wire:loading.attr="disabled"
                                                    wire:target="checkStatus"
                                                    class="px-4 py-2 text-sm text-purple-400 transition-colors duration-150 bg-purple-100 rounded-lg hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                                    <span wire:loading.remove wire:target="checkStatus">
                                                        Verifică Status
                                                    </span>
                                                    <span wire:loading wire:target="checkStatus">
                                                        Se verifică...
                                                    </span>
                                                </button>
                                            </div>

                                            <div class="relative w-full h-2 overflow-hidden bg-gray-200 rounded-full">
                                                <div
                                                    class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-progress">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Final Video -->
                        @if ($videoUrl)
                            <div class="max-w-lg p-6 mx-auto bg-white/5 backdrop-blur-sm rounded-xl">
                                <h2 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-300">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Video Generat
                                </h2>
                                <div class="overflow-hidden rounded-lg aspect-w-9 aspect-h-16">
                                    <video controls class="object-cover w-full">
                                        <source src="{{ $videoUrl }}" type="video/mp4">
                                        Browser-ul tău nu suportă redarea video.
                                    </video>
                                </div>
                                <div class="flex justify-center mt-4">
                                    <a href="{{ $videoUrl }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-6 py-3 text-white transition-colors duration-150 bg-green-500 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Descarcă Video
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
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
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
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
        });
    </script>
</div>
