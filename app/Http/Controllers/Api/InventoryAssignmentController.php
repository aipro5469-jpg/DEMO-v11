<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryAssignment;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryAssignment::with(['item', 'marketer']);

        if ($request->has('marketer_id')) {
            $query->where('marketer_id', $request->input('marketer_id'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'marketer_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = Item::find($validated['item_id']);

        if ($item->total_stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Check for existing assignment
            $assignment = InventoryAssignment::where('marketer_id', $validated['marketer_id'])
                ->where('item_id', $validated['item_id'])
                ->first();

            if ($assignment) {
                // Update existing assignment
                $assignment->increment('quantity', $validated['quantity']);
                if ($assignment->status !== 'active') {
                    $assignment->update(['status' => 'active']);
                }
            } else {
                // Create new Assignment
                $assignment = InventoryAssignment::create([
                    'marketer_id' => $validated['marketer_id'],
                    'item_id' => $validated['item_id'],
                    'quantity' => $validated['quantity'],
                    'status' => 'active',
                ]);
            }

            // Deduct from Global Stock (Movement OUT)
            // Deduct from Global Stock (Movement OUT from Warehouse to Marketer)
            // Source: Admin/Warehouse (null or current user), Destination: Marketer
            $service = new \App\Services\StockMovementService();
            $service->logMovement(
                $validated['item_id'],
                'out', // It's an OUT from warehouse perspective, but also an assignment. 
                // Actually, for "Assignment", it's a transfer. 
                // But the current logic treats it as OUT from main stock.
                // Let's keep it as 'out' from main stock, but specify source/dest.
                $validated['quantity'],
                $request->user(), // Source: Admin who assigned
                \App\Models\User::find($validated['marketer_id']), // Destination: Marketer
                $assignment,
                'Assignment to marketer via App'
            );

            // Update Item Stock
            $item->decrement('total_stock', $validated['quantity']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $assignment,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating assignment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
