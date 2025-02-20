<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\VideoProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

        $filename = Str::slug($video->title) . '.mp4';

        return response($videoContent->body())
            ->header('Content-Type', 'video/mp4')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', strlen($videoContent->body()))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
