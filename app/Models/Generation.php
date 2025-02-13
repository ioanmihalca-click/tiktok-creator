<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'input',
        'output',
        'status',
        'error'
    ];

    protected $casts = [
        'input' => 'array',
        'output' => 'array'
    ];

    public function project()
    {
        return $this->belongsTo(VideoProject::class, 'project_id');
    }
}