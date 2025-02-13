<div class="max-w-2xl p-6 mx-auto">
    <h1 class="mb-6 text-2xl font-bold">Create New TikTok</h1>

    @if (session()->has('message'))
        <div class="px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="generate" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Topic</label>
            <input type="text" wire:model="topic" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
            @error('topic')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Style</label>
            <select wire:model="style" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                <option value="amuzant">Amuzant</option>
                <option value="educational">Educațional</option>
                <option value="motivational">Motivațional</option>
                <option value="storytelling">Profesional</option>
            </select>
        </div>

        <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700"
            wire:loading.attr="disabled" wire:loading.class="opacity-50">
            <span wire:loading.remove>Generate TikTok</span>
            <span wire:loading>Processing...</span>
        </button>
    </form>

      @if($script)
        <div class="mt-8">
              <h2 class="mb-4 text-xl font-bold">
        Generated Script
        <span wire:loading.delay wire:target="generate"
              class="ml-2 text-sm text-gray-500">
            Generating script...
        </span>
    </h2>
            <div class="p-4 bg-gray-100 rounded">
                @foreach($script['scenes'] as $scene)
                    <div class="p-2 mb-4 border-b">
                        <p class="font-medium">Scene {{ $loop->iteration }}</p>
                        <p>Text: {{ $scene['text'] }}</p>
                        <p class="text-sm text-gray-600">
                            Duration: {{ $scene['duration'] }}s | Position: {{ $scene['position'] }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Narration: {{ $scene['narration'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($audioUrl)
        <div class="mt-8">
            <h2 class="mb-4 text-xl font-bold">Generated Narration</h2>
            <audio controls class="w-full">
                <source src="{{ $audioUrl }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        </div>
    @endif

    @if($imageUrl)
        <div class="mt-8">
               <h2 class="mb-4 text-xl font-bold">
        Generated Image
        <span wire:loading.delay wire:target="generate"
              class="ml-2 text-sm text-gray-500">
            Generating image...
        </span>
    </h2>
            <img src="{{ $imageUrl }}" alt="Generated background" class="rounded-lg shadow-lg">
        </div>
    @endif

     @if($isProcessing)
        <div class="mt-8">
            <h2 class="mb-4 text-xl font-bold">Video Status</h2>
            <div class="p-4 bg-blue-100 rounded">
                <p class="text-blue-700">
                    Your video is being processed...
                    <button wire:click="checkStatus" class="px-3 py-1 ml-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                        Check Status
                    </button>
                </p>
            </div>
        </div>

        {{-- Polling automat la fiecare 10 secunde --}}
        <div wire:poll.10s="checkStatus"></div>
    @endif

    @if($videoUrl)
        <div class="mt-8">
            <h2 class="mb-4 text-xl font-bold">Generated Video</h2>
            <div class="aspect-w-9 aspect-h-16">
                <video controls class="w-full rounded-lg shadow-lg">
                    <source src="{{ $videoUrl }}" type="video/mp4">
                    Your browser does not support the video element.
                </video>
            </div>
            <div class="mt-4">
                <a href="{{ $videoUrl }}" 
                   target="_blank" 
                   class="inline-block px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                    Download Video
                </a>
            </div>
        </div>
    @endif
</div>
