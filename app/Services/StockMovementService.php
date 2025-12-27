<?php

namespace App\Services;

use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class StockMovementService
{
    /**
     * Log a stock movement.
     *
     * @param int $itemId
     * @param string $type 'in', 'out', 'return', 'adjustment'
     * @param int $quantity Positive for IN/RETURN, Negative for OUT (automatically handled if positive passed for OUT)
     * @param mixed $source Source model (e.g., User, null for initial stock)
     * @param mixed $destination Destination model (e.g., Client, User)
     * @param mixed $reference Reference model (e.g., InventoryAssignment, InventoryDistribution)
     * @param string|null $notes
     * @return StockMovement
     */
    public function logMovement($itemId, $type, $quantity, $source, $destination, $reference = null, $notes = null)
    {
        // Ensure quantity sign is correct based on type
        if ($type === 'out' && $quantity > 0) {
            $quantity = -$quantity;
        }

        return StockMovement::create([
            'item_id' => $itemId,
            'type' => $type,
            'quantity' => $quantity,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source ? $source->id : null,
            'destination_type' => $destination ? get_class($destination) : null,
            'destination_id' => $destination ? $destination->id : null,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'notes' => $notes,
            'created_by' => Auth::id() ?? 1, // Default to admin if no auth (e.g. seeder)
        ]);
    }
}
