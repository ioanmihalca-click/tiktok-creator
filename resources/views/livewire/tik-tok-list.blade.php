<div class="min-h-screen text-white bg-gradient-to-br from-gray-900 to-gray-800">
    <div class="container px-4 py-8 mx-auto">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1
                class="mb-2 text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
                My TikTok Videos
            </h1>
            <p class="mt-2 text-gray-300">Toate videoclipurile tale generate cu AI</p>
        </div>

        <!-- Notificare despre perioada de stocare -->
        <div class="p-4 mb-6 text-center text-yellow-300 rounded-lg bg-yellow-900/30 backdrop-blur-sm">
            <div class="flex items-center justify-center gap-2 mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span class="font-medium">Notă importantă</span>
            </div>
            <p>Videoclipurile generate sunt stocate pe server doar pentru 24 de ore. Descarcă-ți videoclipurile pentru a
                le păstra!</p>
        </div>

        <!-- Filter and Toggle Section -->
        <div class="flex flex-col items-center justify-between gap-4 mb-8 md:flex-row">
            <div class="flex items-center gap-4">
                <button wire:click="toggleShowExpired"
                    class="px-4 py-2 text-sm font-medium transition-all duration-300 rounded-lg text-gray-300 hover:text-white 
                    {{ $showExpired ? 'bg-purple-700/50 border border-purple-500/50' : 'bg-white/5 border border-white/10' }}">
                    {{ $showExpired ? 'Ascunde expirate' : 'Arată toate' }}
                </button>
                <select wire:model.live="filter"
                    class="px-4 py-2 text-gray-300 border-0 rounded-lg bg-white/5 backdrop-blur-sm focus:ring-2 focus:ring-purple-500">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="rendering">Rendering</option>
                </select>
            </div>

            <!-- Notification Banner for Mobile -->
            <div class="hidden text-sm text-gray-400 md:block">
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-400" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Afișăm implicit doar videoclipurile din ultimele 24 ore
                </span>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="p-4 mb-6 text-green-300 border rounded-lg border-green-500/20 bg-green-900/20">
                {{ session('message') }}
            </div>
        @endif

        @if ($videoProjects->isEmpty())
            <div class="p-8 text-center rounded-lg bg-white/5 backdrop-blur-sm">
                <p class="text-lg text-gray-300">Nu s-au găsit TikTok-uri. Creează primul tău proiect video!</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($videoProjects as $project)
                    <div
                        class="flex flex-col h-full overflow-hidden transition-all duration-200 rounded-xl bg-white/5 backdrop-blur-sm hover:bg-white/10">
                        @if ($project->video_url)
                            <div class="overflow-hidden aspect-w-9 aspect-h-16">
                                <video controls class="object-cover w-full">
                                    <source src="{{ $project->video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-48 bg-white/5">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif

                        <div class="flex flex-col flex-1 p-6">
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="flex-1 text-lg font-semibold text-gray-300">
                                        {{ Str::limit($project->title, 50) }}
                                    </h3>
                                    <span
                                        class="px-2 py-1 ml-2 text-xs font-medium rounded-full whitespace-nowrap
                                        {{ $project->status === 'completed' ? 'bg-green-900/50 text-green-400' : 'bg-yellow-900/50 text-yellow-400' }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>

                                @if (isset($project->script['hashtags']))
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach ($project->script['hashtags'] as $hashtag)
                                            <span
                                                class="px-2 py-1 text-xs font-medium text-purple-400 rounded-full bg-purple-900/50">
                                                {{ $hashtag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-4 text-sm text-gray-400">
                                    <div class="mb-2">
                                        <i class="mr-2 fas fa-clock"></i>
                                        Created {{ $project->created_at->diffForHumans() }}
                                    </div>

                                    <!-- Afișare timp până la expirare -->
                                    <div class="flex items-center mt-2 space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-400"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        @php
                                            $expiryTime = $this->getExpiryTimeRemaining($project->created_at);
                                        @endphp
                                        <span
                                            class="{{ $expiryTime === 'Expirat' ? 'text-red-400' : 'text-yellow-400' }}">
                                            {{ $expiryTime === 'Expirat' ? 'Expirat' : 'Expiră în: ' . $expiryTime }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 mt-4">
                                @if ($project->status === 'completed' && $project->video_url)
                                    <div
                                        class="inline-flex items-center gap-3 px-4 py-3 text-gray-400 transition-all duration-200 border rounded-lg border-white/10 bg-white/5 backdrop-blur-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        <span class="text-sm">
                                            Pentru a descărca videoclipul, apasă pe cele trei puncte din player și
                                            selectează "Download"
                                        </span>
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-3 px-4 py-3 text-gray-400 transition-all duration-200 border rounded-lg border-white/10 bg-white/5 backdrop-blur-sm">
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-xs">
                                            Videoclipul va fi disponibil doar 24 de ore după generare.
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $videoProjects->links() }}
            </div>
        @endif
    </div>
</div>
