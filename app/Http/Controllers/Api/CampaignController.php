<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of active campaigns.
     */
    public function index(Request $request)
    {
        $query = Campaign::with('creator')
            ->where('status', '!=', 'draft');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($campaigns);
    }

    /**
     * Display the specified campaign.
     */
    public function show($id)
    {
        $campaign = Campaign::with(['creator', 'tasks' => function ($query) {
            $query->orderBy('due_date', 'asc');
        }])->find($id);

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        return response()->json($campaign);
    }

    /**
     * Get campaign statistics.
     */
    public function statistics($id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        $tasks = $campaign->tasks;

        $stats = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'done')->count(),
            'in_progress_tasks' => $tasks->where('status', 'in_progress')->count(),
            'todo_tasks' => $tasks->where('status', 'todo')->count(),
            'high_priority_tasks' => $tasks->where('priority', 'high')->count(),
            'progress_percentage' => $campaign->progress,
        ];

        return response()->json($stats);
    }
}
