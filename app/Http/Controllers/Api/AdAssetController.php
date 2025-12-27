<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdAsset;
use Illuminate\Http\Request;

class AdAssetController extends Controller
{
    public function index()
    {
        return AdAsset::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'nullable|string|unique:ad_assets,asset_code',
            'name' => 'required|string|max:255',
            'ad_asset_category_id' => 'nullable|exists:ad_asset_categories,id',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'specs' => 'nullable|array',
            'cost_per_unit' => 'nullable|numeric',
            'quantity' => 'integer|min:0',
            'used_quantity' => 'integer|min:0',
            'status' => 'required|string',
            'condition' => 'nullable|string',
            'location_id' => 'nullable|integer',
            'owner_branch_id' => 'nullable|exists:branches,id',
            'supplier_id' => 'nullable|integer',
            'purchase_date' => 'nullable|date',
            'last_used_at' => 'nullable|date',
            'photos' => 'nullable|array',
            'created_by' => 'nullable|exists:users,id',
        ]);

        $adAsset = AdAsset::create($validated);

        return response()->json($adAsset, 201);
    }

    public function show(AdAsset $adAsset)
    {
        return $adAsset;
    }

    public function update(Request $request, AdAsset $adAsset)
    {
        $validated = $request->validate([
            'asset_code' => 'nullable|string|unique:ad_assets,asset_code,' . $adAsset->id,
            'name' => 'sometimes|required|string|max:255',
            'ad_asset_category_id' => 'nullable|exists:ad_asset_categories,id',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'specs' => 'nullable|array',
            'cost_per_unit' => 'nullable|numeric',
            'quantity' => 'integer|min:0',
            'used_quantity' => 'integer|min:0',
            'status' => 'sometimes|required|string',
            'condition' => 'nullable|string',
            'location_id' => 'nullable|integer',
            'owner_branch_id' => 'nullable|exists:branches,id',
            'supplier_id' => 'nullable|integer',
            'purchase_date' => 'nullable|date',
            'last_used_at' => 'nullable|date',
            'photos' => 'nullable|array',
            'created_by' => 'nullable|exists:users,id',
        ]);

        $adAsset->update($validated);

        return response()->json($adAsset);
    }

    public function destroy(AdAsset $adAsset)
    {
        $adAsset->delete();

        return response()->json(null, 204);
    }
}
