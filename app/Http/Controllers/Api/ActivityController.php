<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Log an activity (e.g., from offline mode).
     */
    public function store(Request $request, ActivityLogger $activityLogger)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'nullable|string',
            'properties' => 'nullable|array',
            'subject_type' => 'nullable|string',
            'subject_id' => 'nullable|integer',
        ]);

        // Resolve subject if provided
        $subject = null;
        if ($request->subject_type && $request->subject_id) {
            // Basic security check: only allow specific models
            $allowedModels = [
                'Task' => \App\Models\Task::class,
                'Campaign' => \App\Models\Campaign::class,
                'Client' => \App\Models\Client::class,
            ];

            if (array_key_exists($request->subject_type, $allowedModels)) {
                $subject = $allowedModels[$request->subject_type]::find($request->subject_id);
            }
        }

        $activity = $activityLogger->log(
            $request->type,
            $subject,
            $request->description,
            $request->properties
        );

        return response()->json([
            'message' => 'Activity logged successfully',
            'data' => $activity
        ], 201);
    }
}
