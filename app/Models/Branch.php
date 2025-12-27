<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'manager_id',
        'is_active',
        'parent_id',
        'city_id',
        'address',
        'gps_location',
        'manager_name',
        'manager_phone',
        'created_by',
    ];

    protected $casts = [
        'gps_location' => 'array',
        'manager_phone' => 'array',
        'is_active' => 'boolean',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Branch $branch) {
            if (auth()->check()) {
                $branch->created_by = auth()->id();
            }
        });
    }
}
