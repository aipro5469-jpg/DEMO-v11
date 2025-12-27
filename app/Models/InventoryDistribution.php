<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDistribution extends Model
{
    protected $fillable = [
        'marketer_id',
        'client_id',
        'item_id',
        'quantity',
        'distributed_at',
        'notes',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
