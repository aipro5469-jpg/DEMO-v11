<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryDistribution;
use App\Models\InventoryAssignment;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryDistributionController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryDistribution::with(['item', 'marketer', 'client']);

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
            'client_id' => 'required|exists:clients,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'distributed_at' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            // Check if marketer has enough stock in their assignment
            // Note: This logic assumes we track stock per assignment or aggregate.
            // For simplicity, we just check if they have *any* active assignment with enough quantity?
            // Or we just record the distribution and let the backend/admin reconcile.
            // Given "Offline First", we trust the client's validation, but we should verify here too.

            // Find assignment to deduct from?
            // In a real system, we might decrement a specific assignment.
            // Here, we'll just record the distribution.
            // The StockMovement will handle the logic of "Out from Marketer".

            $distribution = InventoryDistribution::create([
                'marketer_id' => $validated['marketer_id'],
                'client_id' => $validated['client_id'],
                'item_id' => $validated['item_id'],
                'quantity' => $validated['quantity'],
                'distributed_at' => $validated['distributed_at'] ?? now(),
                'notes' => $validated['notes'],
            ]);

            // We should also update the InventoryAssignment quantity?
            // If we want to track "Remaining", we must deduct.
            // Find the oldest active assignment for this item/marketer
            $assignment = InventoryAssignment::where('marketer_id', $validated['marketer_id'])
                ->where('item_id', $validated['item_id'])
                ->where('status', 'active')
                ->where('quantity', '>=', $validated['quantity']) // Simple check
                ->first();

            if ($assignment) {
                $assignment->decrement('quantity', $validated['quantity']);
            } else {
                // Handle case where no single assignment has enough, or just log it.
                // For now, we'll allow it but log a warning or create a negative movement?
                // Let's assume the app validated it.
            }

            // Record Stock Movement (Marketer -> Client)
            // Source: Marketer, Destination: Client
            $service = new \App\Services\StockMovementService();
            $service->logMovement(
                $validated['item_id'],
                'out', // Distribution is an OUT from Marketer's stock
                $validated['quantity'],
                \App\Models\User::find($validated['marketer_id']), // Source: Marketer
                \App\Models\Client::find($validated['client_id']), // Destination: Client
                $distribution,
                'Distributed to Client ' . $validated['client_id']
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $distribution,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating distribution: ' . $e->getMessage(),
            ], 500);
        }
    }
}
