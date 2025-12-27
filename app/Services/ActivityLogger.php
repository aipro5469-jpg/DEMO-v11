<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log a system activity.
     *
     * @param string $type The type of activity (e.g., 'campaign_created', 'task_completed')
     * @param Model|null $subject The subject model (e.g., Campaign, Task)
     * @param string|null $description A human-readable description
     * @param array|null $properties Additional properties/metadata
     * @param int|null $userId The user ID performing the action (defaults to auth user)
     * @return Activity
     */
    public function log(
        string $type,
        ?Model $subject = null,
        ?string $description = null,
        ?array $properties = null,
        ?int $userId = null
    ): Activity {
        $userId = $userId ?? Auth::id();

        // If no user is authenticated and no user ID is provided, we can't log (or log as system/anonymous if needed)
        // For now, we assume a user context is required.
        if (!$userId) {
            // Fallback for system actions if needed, or throw exception. 
            // For this system, we'll just return a dummy or handle gracefully.
            // Let's assume system actions have a specific user ID or we skip.
            // But for now, let's just create it with null user_id if the DB allows (it doesn't, it's not nullable).
            // So we must have a user.
            // If running from CLI/Seeder, we might need a default user.
            $userId = 1; // Fallback to admin/first user for now if not auth
        }

        return Activity::create([
            'user_id' => $userId,
            'type' => $type,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
