<?php

namespace App\Livewire;

use App\Models\VideoProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class TikTokList extends Component
{
    use WithPagination;

    public $filter = '';

    protected $queryString = ['filter'];

    public function render()
    {
        $query = VideoProject::where('user_id', Auth::id());
        
        if ($this->filter) {
            $query->where('status', $this->filter);
        }
        
        $videoProjects = $query
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('livewire.tik-tok-list', [
            'videoProjects' => $videoProjects,
        ]);
    }

    public function deleteVideo($id)
    {
        $video = VideoProject::where('user_id', Auth::id())->findOrFail($id);
        
        if ($video->output_path) {
            Storage::delete($video->output_path);
        }
        
        $video->delete();
        
        session()->flash('message', 'Video deleted successfully.');
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }
}
