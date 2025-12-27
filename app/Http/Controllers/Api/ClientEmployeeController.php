<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientEmployee;
use Illuminate\Http\Request;

class ClientEmployeeController extends Controller
{
    public function index()
    {
        // Return all employees for now, or filter by user permissions
        return ClientEmployee::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'role' => 'nullable|string',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'is_decision_maker' => 'boolean',
            'notes' => 'nullable|string',
            'previous_client_id' => 'nullable|exists:clients,id',
        ]);

        if (isset($validated['phone']) && is_array($validated['phone'])) {
            $validated['phone'] = json_encode($validated['phone']);
        }

        // Map is_decision_maker to is_contact_point
        if (isset($validated['is_decision_maker'])) {
            $validated['is_contact_point'] = $validated['is_decision_maker'];
            unset($validated['is_decision_maker']);
        }

        $validated['created_by'] = $request->user()->id;

        $employee = ClientEmployee::create($validated);

        return response()->json($employee, 201);
    }

    public function show(ClientEmployee $clientEmployee)
    {
        return $clientEmployee;
    }

    public function update(Request $request, ClientEmployee $clientEmployee)
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|required|exists:clients,id',
            'name' => 'sometimes|required|string|max:255',
            'role' => 'nullable|string',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'is_decision_maker' => 'boolean',
            'notes' => 'nullable|string',
            'previous_client_id' => 'nullable|exists:clients,id',
        ]);

        if (isset($validated['phone']) && is_array($validated['phone'])) {
            $validated['phone'] = json_encode($validated['phone']);
        }

        // Map is_decision_maker to is_contact_point
        if (isset($validated['is_decision_maker'])) {
            $validated['is_contact_point'] = $validated['is_decision_maker'];
            unset($validated['is_decision_maker']);
        }

        $clientEmployee->update($validated);

        return response()->json($clientEmployee);
    }

    public function destroy(ClientEmployee $clientEmployee)
    {
        $clientEmployee->delete();

        return response()->json(null, 204);
    }
}
