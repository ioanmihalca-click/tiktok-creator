<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'script',
        'settings',
        'status',
        'image_url',
        'image_cloudinary_id',
        'audio_url',
        'audio_cloudinary_id',
        'video_url',
        'render_id',
        'category_id',
        'environment_type',
        'has_watermark',
        'voice_id'
    ];

    protected $casts = [
        'script' => 'array',
        'settings' => 'array',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(VideoImage::class)->orderBy('order'); // Ordonare după câmpul 'order'
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generations(): HasMany
    {
        return $this->hasMany(Generation::class, 'project_id');
    }
}
