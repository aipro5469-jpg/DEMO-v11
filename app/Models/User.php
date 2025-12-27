<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \Laravel\Sanctum\HasApiTokens, \Spatie\Permission\Traits\HasRoles;

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->is_active;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'is_active',
        'username',
        'phone',
        'preferences',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->uuid = $user->uuid ?? (string) \Illuminate\Support\Str::uuid();
        });
    }

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
            'preferences' => 'array',
        ];
    }

    protected function phone(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return [];
                }

                // Try to decode JSON
                $decoded = json_decode($value, true);

                // If it's a valid JSON array, return it
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }

                // Otherwise, treat it as a legacy single phone number and wrap it
                return [['number' => $value]];
            },
            set: fn($value) => json_encode($value),
        );
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }
}
