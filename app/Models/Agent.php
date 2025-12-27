<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'phone',
        'email',
        'region',
        'is_active',
        'branch_id',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function evaluations(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Evaluation::class, 'evaluable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::creating(function (Agent $agent) {
            if (auth()->check()) {
                $agent->created_by = auth()->id();
            }
        });
    }
}
