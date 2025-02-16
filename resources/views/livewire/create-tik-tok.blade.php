<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl p-6 mx-auto">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">TikTok Creator</h1>
            <p class="mt-2 text-gray-600">Generează videoclipuri TikTok cu AI în câțiva pași simpli</p>
        </div>

        <!-- Notifications -->
        @if (session()->has('message'))
            <div class="p-4 mb-6 transition-all duration-300 bg-green-100 border-l-4 border-green-500 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 mb-6 transition-all duration-300 bg-red-100 border-l-4 border-red-500 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Creator Card -->
        <div class="p-6 bg-white shadow-sm rounded-xl">
            <form wire:submit.prevent="generate" class="space-y-6">
                <!-- Category Select -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Alege Categoria
                        </span>
                    </label>
                    <select wire:model="categorySlug" 
                            class="w-full px-4 py-2 text-gray-700 border-gray-300 rounded-lg bg-gray-50 focus:ring-purple-500 focus:border-purple-500">
                        @foreach($categories as $mainSlug => $mainCategory)
                            @if(isset($mainCategory['name']) && isset($mainCategory['subcategories']))
                                <optgroup label="{{ $mainCategory['name'] }}" class="font-medium">
                                    @foreach($mainCategory['subcategories'] as $subSlug => $subCategory)
                                        @if(isset($subCategory['name']))
                                            @if(isset($subCategory['subcategories']) && !empty($subCategory['subcategories']))
                                                <optgroup label="╰─ {{ $subCategory['name'] }}" class="ml-2">
                                                    @foreach($subCategory['subcategories'] as $subSubSlug => $subSubCat)
                                                        <option value="{{ $subSubSlug }}">╰── {{ $subSubCat['name'] }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                <option value="{{ $subSlug }}">╰─ {{ $subCategory['name'] }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    @error('categorySlug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Topic Input with AI Generation -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Topic
                        </span>
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" 
                                   wire:model="topic" 
                                   class="w-full px-4 py-2 border-gray-300 rounded-lg bg-gray-50 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Despre ce vrei să creezi videoclipul?">
                        </div>
                        <button type="button"
                                wire:click="generateTopic"
                                class="flex items-center px-4 py-2 text-white transition-colors duration-150 bg-purple-500 rounded-lg hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-75">
                            <span wire:loading.remove wire:target="generateTopic">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="generateTopic">
                                <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                    @error('topic')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Generate Button -->
                <button type="submit" 
                        class="w-full px-6 py-3 text-white transition-colors duration-150 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                        wire:loading.attr="disabled"
                        wire:target="generate"
                        wire:loading.class="opacity-75">
                    <span wire:loading.remove wire:target="generate" class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Generează TikTok
                    </span>
                    <span wire:loading wire:target="generate" class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Se procesează...
                    </span>
                </button>
            </form>
        </div>

        <!-- Preview Section -->
        @if($script || $audioUrl || $imageUrl || $videoUrl || $isProcessing)
            <div class="mt-8 space-y-8">
                <!-- Script Preview -->
                @if($script)
                    <div class="p-6 bg-white shadow-sm rounded-xl">
                        <h2 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Script Generat
                        </h2>
                        <div class="space-y-4">
                            @foreach($script['scenes'] as $scene)
                                <div class="p-4 transition-all duration-200 border border-gray-100 rounded-lg hover:border-purple-100 hover:bg-purple-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="px-2 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full">
                                            Scena {{ $loop->iteration }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $scene['duration'] }}s
                                        </span>
                                    </div>
                                    <p class="mb-2 text-gray-800">{{ $scene['text'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $scene['narration'] }}</p>
                                </div>
                            @endforeach

                            @if(isset($script['hashtags']))
                                <div class="flex flex-wrap gap-2 mt-4">
                                    @foreach($script['hashtags'] as $hashtag)
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
                @if($audioUrl)
                    <div class="p-6 bg-white shadow-sm rounded-xl">
                        <h2 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 001.414 1.414m2.828-9.9a9 9 0 012.828-2.828" />
                            </svg>
                            Narare Generată
                        </h2>
                        <div class="p-4 rounded-lg bg-gray-50">
                            <audio controls class="w-full">
                                <source src="{{ $audioUrl }}" type="audio/mpeg">
                                Browser-ul tău nu suportă redarea audio.
                            </audio>
                        </div>
                    </div>
                @endif

                <!-- Image Preview -->
                @if($imageUrl)
                    <div class="p-6 bg-white shadow-sm rounded-xl">
                        <h2 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                           <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Imagine Generată
                        </h2>
                        <div class="overflow-hidden rounded-lg">
                            <img src="{{ $imageUrl }}" 
                                 alt="Imagine de fundal generată" 
                                 class="object-cover w-full transition-transform duration-300 hover:scale-105">
                        </div>
                    </div>
                @endif

                <!-- Video Status -->
                @if($isProcessing)
                <div wire:poll.5s="checkStatus">
                    <div class="p-6 bg-white shadow-sm rounded-xl">
                        <h2 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Status Video
                        </h2>
                        <div class="p-4 rounded-lg bg-blue-50">
                            <div class="flex items-center justify-between">
                                <p class="text-blue-700">
                                    Videoclipul tău este în curs de procesare...
                                </p>
                                <button wire:click="checkStatus" 
                                        wire:loading.attr="disabled"
                                        wire:target="checkStatus"
                                        class="px-4 py-2 text-white transition-colors duration-150 bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <span wire:loading.remove wire:target="checkStatus">
                                        Verifică Status
                                    </span>
                                    <span wire:loading wire:target="checkStatus">
                                        Se verifică...
                                    </span>
                                </button>
                            </div>
                            <div class="w-full h-2 mt-4 overflow-hidden bg-blue-200 rounded-full">
                                <div class="w-1/2 h-full bg-blue-500 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Final Video -->
                @if($videoUrl)
                    <div class="p-6 bg-white shadow-sm rounded-xl">
                        <h2 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
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
                            <a href="{{ $videoUrl }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-6 py-3 text-white transition-colors duration-150 bg-green-500 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
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
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('videoReady', () => {
            // Forțăm un refresh al secțiunii video
            Livewire.dispatch('refresh');
        });
    });
</script>