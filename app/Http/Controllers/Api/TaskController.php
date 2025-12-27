<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskCheckpoint;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * List tasks assigned to the current user.
     */
    public function index(Request $request)
    {
        $query = Task::with(['campaign', 'checkpoints'])
            ->where('assigned_to', auth()->id());

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date (e.g., today)
        if ($request->has('date')) {
            $query->whereDate('due_date', $request->date);
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(20);

        return response()->json($tasks);
    }

    /**
     * Show task details.
     */
    public function show($id)
    {
        $task = Task::with(['campaign', 'checkpoints', 'assignee'])
            ->where('assigned_to', auth()->id())
            ->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found or not assigned to you'], 404);
        }

        return response()->json($task);
    }

    /**
     * Complete a checkpoint.
     */
    public function completeCheckpoint(Request $request, $taskId, $checkpointId, ActivityLogger $activityLogger)
    {
        $task = Task::where('assigned_to', auth()->id())->find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $checkpoint = TaskCheckpoint::where('task_id', $task->id)->find($checkpointId);

        if (!$checkpoint) {
            return response()->json(['message' => 'Checkpoint not found'], 404);
        }

        $request->validate([
            'data' => 'nullable|array', // For photo paths, text input, etc.
        ]);

        DB::transaction(function () use ($checkpoint, $request, $activityLogger, $task) {
            $checkpoint->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => auth()->id(),
                'data' => $request->data,
            ]);

            // Log activity
            $activityLogger->log(
                'checkpoint_completed',
                $task,
                "Completed checkpoint: {$checkpoint->title}",
                ['checkpoint_id' => $checkpoint->id]
            );
        });

        // Refresh task to get updated progress
        $task->refresh();

        return response()->json([
            'message' => 'Checkpoint completed',
            'task' => $task->load('checkpoints'),
        ]);
    }
}
