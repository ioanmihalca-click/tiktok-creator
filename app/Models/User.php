<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\VideoProject;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function videoProjects(): HasMany
    {
        return $this->hasMany(VideoProject::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, 'ioanclickmihalca@gmail.com');
    }

    public function userCredit()
    {
        return $this->hasOne(UserCredit::class);
    }

    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class);
    }

    // Create a method to check if user has credits
    public function hasCreditsAvailable()
    {
        return $this->userCredit?->total_available_credits > 0;
    }

    // Create a method to deduct credits
    public function deductCredit()
    {
        if (!$this->userCredit) {
            $this->userCredit()->create([
                'free_credits' => 3
            ]);
        }

        if ($this->userCredit->available_free_credits > 0) {
            $this->userCredit->increment('used_free_credits');
            $this->creditTransactions()->create([
                'transaction_type' => 'usage',
                'amount' => -1,
                'description' => 'Used free credit for video generation'
            ]);
            return 'free';
        } elseif ($this->userCredit->available_credits > 0) {
            $this->userCredit->increment('used_credits');
            $this->creditTransactions()->create([
                'transaction_type' => 'usage',
                'amount' => -1,
                'description' => 'Used paid credit for video generation'
            ]);
            return 'paid';
        }

        return false;
    }
}
