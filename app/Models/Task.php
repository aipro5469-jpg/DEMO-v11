<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $table = 'marketing_tasks';

    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'priority',
        'due_date',
        'status',
        'progress',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(TaskCheckpoint::class, 'task_id');
    }

    public function updateProgress(): void
    {
        $totalCheckpoints = $this->checkpoints()->count();

        if ($totalCheckpoints === 0) {
            $this->update(['progress' => 0]);
            return;
        }

        $completedCheckpoints = $this->checkpoints()->where('status', 'completed')->count();
        $progress = (int) round(($completedCheckpoints / $totalCheckpoints) * 100);

        $this->update(['progress' => $progress]);

        // Also update status if progress is 100
        if ($progress === 100 && $this->status !== 'done') {
            $this->update(['status' => 'done']);
        } elseif ($progress > 0 && $progress < 100 && $this->status === 'todo') {
            $this->update(['status' => 'in_progress']);
        }
    }
}
