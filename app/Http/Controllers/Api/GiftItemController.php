<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GiftItem;
use Illuminate\Http\Request;

class GiftItemController extends Controller
{
    public function index()
    {
        return GiftItem::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|unique:gift_items,code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string',
            'unit' => 'nullable|string',
            'cost_per_unit' => 'nullable|numeric',
            'total_stock' => 'integer|min:0',
        ]);

        $giftItem = GiftItem::create($validated);

        return response()->json($giftItem, 201);
    }

    public function show(GiftItem $giftItem)
    {
        return $giftItem;
    }

    public function update(Request $request, GiftItem $giftItem)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|unique:gift_items,code,' . $giftItem->id,
            'name' => 'sometimes|required|string|max:255',
            'category' => 'nullable|string',
            'unit' => 'nullable|string',
            'cost_per_unit' => 'nullable|numeric',
            'total_stock' => 'integer|min:0',
        ]);

        $giftItem->update($validated);

        return response()->json($giftItem);
    }

    public function destroy(GiftItem $giftItem)
    {
        $giftItem->delete();

        return response()->json(null, 204);
    }
}
