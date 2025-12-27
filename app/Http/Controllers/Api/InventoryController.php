<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\InventoryAssignment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function returnAssignment(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:inventory_assignments,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $assignment = InventoryAssignment::where('id', $request->assignment_id)
            ->where('marketer_id', $user->id)
            ->firstOrFail();

        if ($assignment->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient quantity'], 400);
        }

        DB::transaction(function () use ($assignment, $request, $user) {
            // 1. Create Stock Movement (RETURN)
            StockMovement::create([
                'item_id' => $assignment->item_id,
                'type' => 'return',
                'quantity' => $request->quantity,
                'reference_type' => InventoryAssignment::class,
                'reference_id' => $assignment->id,
                'notes' => $request->notes ?? 'إرجاع عهدة من المسوق: ' . $user->name,
                'created_by' => $user->id,
            ]);

            // 2. Decrement Assignment Quantity
            $assignment->decrement('quantity', $request->quantity);
        });

        return response()->json(['message' => 'Inventory returned successfully']);
    }
}
