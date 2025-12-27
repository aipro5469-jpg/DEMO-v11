<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'cost_per_unit',
        'total_stock',
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:2',
        'total_stock' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'GFT-' . strtoupper(Str::random(8));
            }
        });
    }
}
