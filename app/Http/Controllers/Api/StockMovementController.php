<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['item', 'source', 'destination', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($request->has('item_id')) {
            $query->where('item_id', $request->input('item_id'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('scope')) {
            $scope = $request->input('scope');
            $userId = $request->user()->id;

            if ($scope === 'my_operations') {
                $query->where('created_by', $userId);
            } elseif ($scope === 'others') {
                $query->where('created_by', '!=', $userId);
            }
        }

        // Pagination
        return response()->json([
            'success' => true,
            'data' => $query->paginate(20),
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:in,out,adjustment,return',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'source_type' => 'nullable|string',
            'source_id' => 'nullable|integer',
            'destination_type' => 'nullable|string',
            'destination_id' => 'nullable|integer',
        ]);

        $validated['created_by'] = $request->user()->id;

        $movement = StockMovement::create($validated);

        return response()->json([
            'success' => true,
            'data' => $movement->load(['item', 'source', 'destination', 'creator']),
        ], 201);
    }
}
