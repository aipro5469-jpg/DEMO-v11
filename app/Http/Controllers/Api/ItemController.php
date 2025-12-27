<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'item_type' => 'required|in:gift,sale',
            'unit' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $item = Item::create([
            'name' => $validated['name'],
            'code' => null, // Code is no longer used
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'item_type' => $validated['item_type'],
            'unit' => $validated['unit'],
            'status' => $validated['status'],
            'total_stock' => 0, // Initial stock is 0
        ]);

        return response()->json([
            'success' => true,
            'data' => $item,
        ], 201);
    }
}
