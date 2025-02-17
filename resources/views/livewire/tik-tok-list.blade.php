<div class="container px-4 py-8 mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">My TikTok Videos</h1>
        <div class="flex gap-2">
            <select wire:model.live="filter" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="rendering">Rendering</option>
            </select>
        </div>
    </div>

    @if ($videoProjects->isEmpty())
        <div class="p-8 text-center rounded-lg bg-gray-50">
            <p class="text-lg text-gray-600">No TikToks found. Create your first video project!</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($videoProjects as $project)
                <div class="transition-shadow duration-200 bg-white rounded-lg shadow-md hover:shadow-lg">
                    @if($project->output_path)
                        <div class="overflow-hidden rounded-t-lg aspect-w-9 aspect-h-16">
                            <video controls class="object-cover w-full">
                                <source src="{{ Storage::url($project->output_path) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-48 bg-gray-100 rounded-t-lg">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <h3 class="flex-1 mb-2 text-lg font-semibold text-gray-800">
                                {{ Str::limit($project->title, 50) }}
                            </h3>
                            <span class="px-2 py-1 ml-2 text-xs whitespace-nowrap rounded-full
                                {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </div>
                        
                        <div class="mt-4 text-sm text-gray-600">
                            <div class="mb-2">
                                <i class="mr-2 fas fa-clock"></i>
                                Created {{ $project->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            @if($project->status === 'completed' && $project->output_path)
                                <a href="{{ Storage::url($project->output_path) }}" download 
                                   class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-white transition-colors duration-150 bg-green-500 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Descarcă Video
                                </a>
                            @endif
                            <button wire:click="deleteVideo({{ $project->id }})"
                                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-red-700 transition-colors duration-150 bg-red-100 rounded-lg hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                <i class="fas fa-trash-alt"></i>
                            </button>
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
