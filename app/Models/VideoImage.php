<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoImage extends Model
{
    use HasFactory;

    protected $fillable = ['video_project_id', 'url', 'cloudinary_id', 'start', 'duration', 'order'];

    public function videoProject(): BelongsTo
    {
        return $this->belongsTo(VideoProject::class);
    }
}
