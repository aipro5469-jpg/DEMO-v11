<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\StockMovement;
use App\Models\User;

class InventoryAssignment extends Model
{
    protected $fillable = [
        'marketer_id',
        'item_id',
        'quantity',
        'status',
    ];

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    protected static function booted()
    {
        static::created(function ($assignment) {
            // Create Stock Movement (OUT)
            StockMovement::create([
                'item_id' => $assignment->item_id,
                'type' => 'out',
                'quantity' => $assignment->quantity,
                'reference_type' => self::class,
                'reference_id' => $assignment->id,
                'notes' => 'صرف عهدة للمسوق: ' . ($assignment->marketer->name ?? 'Unknown'),
                'created_by' => Auth::id() ?? 1,
            ]);
        });
    }
}
