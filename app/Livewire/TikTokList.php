<?php

namespace App\Livewire;

use App\Models\VideoProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class TikTokList extends Component
{
    use WithPagination;

    public $filter = '';
    public $showExpired = false; // Nou flag pentru a controla dacă arătăm proiecte expirate

    protected $queryString = ['filter', 'showExpired'];

    public function render()
    {
        $query = VideoProject::where('user_id', Auth::id());
        
        if ($this->filter) {
            $query->where('status', $this->filter);
        }
        
        // Filtrăm proiectele create în ultimele 24 ore, dacă showExpired este false
        if (!$this->showExpired) {
            $query->where('created_at', '>=', Carbon::now()->subHours(24));
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

    public function toggleShowExpired()
    {
        $this->showExpired = !$this->showExpired;
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    // Metodă pentru a verifica dacă un proiect video va expira curând
    public function getExpiryTimeRemaining($createdAt)
    {
        $expiryTime = Carbon::parse($createdAt)->addHours(24);
        $now = Carbon::now();
        
        if ($now->greaterThan($expiryTime)) {
            return 'Expirat';
        }
        
        $minutesRemaining = $now->diffInMinutes($expiryTime, false);
        $hoursRemaining = floor($minutesRemaining / 60);
        $mins = $minutesRemaining % 60;
        
        return $hoursRemaining . 'h ' . $mins . 'm';
    }
}