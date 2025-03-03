<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credits',
        'used_credits',
        'free_credits',
        'used_free_credits'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAvailableCreditsAttribute()
    {
        return $this->credits - $this->used_credits;
    }

    public function getAvailableFreeCreditsAttribute()
    {
        return $this->free_credits - $this->used_free_credits;
    }

    public function getTotalAvailableCreditsAttribute()
    {
        return $this->getAvailableCreditsAttribute() + $this->getAvailableFreeCreditsAttribute();
    }
}
