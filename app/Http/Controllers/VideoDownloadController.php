<?php

namespace App\Http\Controllers;

use App\Models\VideoProject;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class VideoDownloadController extends Controller
{
    public function download($id)
    {
        $video = VideoProject::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if (!$video->video_url) {
            return back()->with('error', 'Video URL not found.');
        }

        $videoContent = Http::timeout(60)->get($video->video_url);
        
        if (!$videoContent->successful()) {
            return back()->with('error', 'Could not download video.');
        }

        $filename = slug($video->title) . '.mp4';
        
        return response($videoContent->body())
            ->header('Content-Type', 'video/mp4')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}

function slug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}
