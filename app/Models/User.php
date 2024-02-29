<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName, HasAvatar, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     *
     */
    protected $fillable = [
        'avatar',
        'user_name',
        'first_name',
        'last_name',
        'email',
        'is_admin',
        'is_activated',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getRouteKeyName(): string
    {
        return 'user_name';
    }

    public function isActive()
    {
        return $this->is_activated;
    }

    // Scopes for filtering users by activation status

    public function scopeActive($query)
    {
        return $query->where('is_activated', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_activated', false);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar;
    }

    public function borrows(): HasMany
    {
        return $this->HasMany(Borrow::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_activated' => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return str_ends_with($this->email, '@gmail.com') && $this->is_activated == True && $this->is_admin == True;
        }
        else if ($panel->getId() === 'student')  {
            return str_ends_with($this->email, '@gmail.com') && $this->is_activated == True && $this->is_admin == False;
        }
        return true;
    }
}
