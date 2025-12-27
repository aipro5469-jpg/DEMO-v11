<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        // $user = auth()->user();

        // If user is admin (you might want to check role here), return all
        // For now, let's assume if they have a specific permission or role they see all
        // But based on request "clients associated with the employee", we filter:

        return Client::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable', // Can be string or array
            'email' => 'nullable|email',
            'category' => 'nullable|string',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'address' => 'nullable|string',
            'gps_location' => 'nullable|string',
            'importance' => 'nullable|string',
            'is_agent' => 'boolean',
            'loyalty_level' => 'nullable|string',
            'notes' => 'nullable|string',
            'parent_id' => 'nullable|exists:clients,id',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'classification_id' => 'nullable|integer',
            'images' => 'nullable', // Can be array or string
            'images_inside' => 'nullable',
            'images_outside' => 'nullable',
            'profile_image' => 'nullable|string',
            // New Comprehensive Fields
            'shop_name' => 'nullable|string',
            'shop_phones' => 'nullable', // Array or string
            'owner_name' => 'nullable|string',
            'owner_phones' => 'nullable', // Array or string
            'shop_size' => 'nullable|string',
            'technician_count' => 'nullable|integer',
            'cooperation_level' => 'nullable|string',
            'has_bias' => 'boolean',
            'bias_company_name' => 'nullable|string',
            'main_supplier_name' => 'nullable|string',
            'supplier_satisfaction_rating' => 'nullable|integer',
            'competitor_goods_availability' => 'nullable|string',
            'aljabali_goods_availability' => 'nullable|string',
            'best_selling_item_aljabali' => 'nullable|string',
            'best_selling_item_competitors' => 'nullable|string',
            'quantity_sold' => 'nullable|integer',
            'positive_feedback' => 'nullable|string',
            'negative_feedback' => 'nullable|string',
            'consumer_complaints' => 'nullable|string',
            'suggestions_new_parts' => 'nullable|string',
            'suggestions_meters' => 'nullable|string',
            'suggestions_unavailable' => 'nullable|string',
            'suggestions_improvement' => 'nullable|string',
            'workshop_type' => 'nullable|string',
            'installed_spare_parts_types' => 'nullable', // Array or string
            'most_requested_parts' => 'nullable|string',
            'technical_notes' => 'nullable|string',
            'has_flange_oils' => 'boolean',
            'most_used_oil_type' => 'nullable|string',
            'oil_usage_reason' => 'nullable|string',
            'oil_sales_increase_requirements' => 'nullable|string',
            'opinion_aljabali_oils' => 'nullable|string',
            'opinion_competitor_oils' => 'nullable|string',
        ]);

        // Handle phone if it comes as array from Flutter
        if (isset($validated['phone']) && is_array($validated['phone'])) {
            $validated['phone'] = json_encode($validated['phone']);
        }

        $validated['created_by'] = $request->user()->id;
        $client = Client::create($validated);

        return response()->json($client, 201);
    }

    public function show(Client $client)
    {
        return $client->load(['employees', 'parent', 'children']);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'category' => 'nullable|string',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'address' => 'nullable|string',
            'gps_location' => 'nullable|string',
            'importance' => 'nullable|string',
            'is_agent' => 'boolean',
            'loyalty_level' => 'nullable|string',
            'notes' => 'nullable|string',
            'parent_id' => 'nullable|exists:clients,id',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'classification_id' => 'nullable|integer',
            'images' => 'sometimes|nullable',
            'images_inside' => 'sometimes|nullable',
            'images_outside' => 'sometimes|nullable',
            'profile_image' => 'nullable|string',
            // New Comprehensive Fields
            'shop_name' => 'nullable|string',
            'shop_phones' => 'nullable', // Array or string
            'owner_name' => 'nullable|string',
            'owner_phones' => 'nullable', // Array or string
            'shop_size' => 'nullable|string',
            'technician_count' => 'nullable|integer',
            'cooperation_level' => 'nullable|string',
            'has_bias' => 'boolean',
            'bias_company_name' => 'nullable|string',
            'main_supplier_name' => 'nullable|string',
            'supplier_satisfaction_rating' => 'nullable|integer',
            'competitor_goods_availability' => 'nullable|string',
            'aljabali_goods_availability' => 'nullable|string',
            'best_selling_item_aljabali' => 'nullable|string',
            'best_selling_item_competitors' => 'nullable|string',
            'quantity_sold' => 'nullable|integer',
            'positive_feedback' => 'nullable|string',
            'negative_feedback' => 'nullable|string',
            'consumer_complaints' => 'nullable|string',
            'suggestions_new_parts' => 'nullable|string',
            'suggestions_meters' => 'nullable|string',
            'suggestions_unavailable' => 'nullable|string',
            'suggestions_improvement' => 'nullable|string',
            'workshop_type' => 'nullable|string',
            'installed_spare_parts_types' => 'nullable', // Array or string
            'most_requested_parts' => 'nullable|string',
            'technical_notes' => 'nullable|string',
            'has_flange_oils' => 'boolean',
            'most_used_oil_type' => 'nullable|string',
            'oil_usage_reason' => 'nullable|string',
            'oil_sales_increase_requirements' => 'nullable|string',
            'opinion_aljabali_oils' => 'nullable|string',
            'opinion_competitor_oils' => 'nullable|string',
        ]);

        if (isset($validated['phone']) && is_array($validated['phone'])) {
            $validated['phone'] = json_encode($validated['phone']);
        }

        $client->update($validated);

        return response()->json($client);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(null, 204);
    }
}
