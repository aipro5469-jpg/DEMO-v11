<?php

namespace App\Observers;

use App\Models\TaskCheckpoint;

class TaskCheckpointObserver
{
    /**
     * Handle the TaskCheckpoint "saved" event.
     */
    public function saved(TaskCheckpoint $taskCheckpoint): void
    {
        $taskCheckpoint->task->updateProgress();
    }

    /**
     * Handle the TaskCheckpoint "deleted" event.
     */
    public function deleted(TaskCheckpoint $taskCheckpoint): void
    {
        $taskCheckpoint->task->updateProgress();
    }

    /**
     * Handle the TaskCheckpoint "restored" event.
     */
    public function restored(TaskCheckpoint $taskCheckpoint): void
    {
        //
    }

    /**
     * Handle the TaskCheckpoint "force deleted" event.
     */
    public function forceDeleted(TaskCheckpoint $taskCheckpoint): void
    {
        //
    }
}
