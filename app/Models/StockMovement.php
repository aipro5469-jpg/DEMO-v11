<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'source_type',
        'source_id',
        'destination_type',
        'destination_id',
        'notes',
        'created_by',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function destination(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::created(function ($movement) {
            $item = $movement->item;
            if ($movement->type === 'in' || $movement->type === 'return') {
                $item->increment('total_stock', $movement->quantity);
            } elseif ($movement->type === 'out') {
                $item->decrement('total_stock', $movement->quantity);
            } elseif ($movement->type === 'adjustment') {
                // For adjustment, quantity can be positive or negative
                // If positive, it adds. If negative, it subtracts.
                // But usually adjustment is "set to X" or "add/sub X".
                // Let's assume adjustment quantity is the CHANGE amount.
                if ($movement->quantity > 0) {
                    $item->increment('total_stock', $movement->quantity);
                } else {
                    $item->decrement('total_stock', abs($movement->quantity));
                }
            }
        });
    }
}
