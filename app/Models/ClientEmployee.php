<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'role',
        'phone',
        'email',
        'is_contact_point',
        'created_by',
    ];

    protected $casts = [
        'phone' => 'array',
        'is_contact_point' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::creating(function (ClientEmployee $employee) {
            if (auth()->check()) {
                $employee->created_by = auth()->id();
            }
        });
    }
}
