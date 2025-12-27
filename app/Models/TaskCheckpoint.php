<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskCheckpoint extends Model
{
    use HasFactory;

    protected $table = 'marketing_task_checkpoints';

    protected $fillable = [
        'task_id',
        'title',
        'type',
        'is_required',
        'status',
        'completed_at',
        'completed_by',
        'data',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'completed_at' => 'datetime',
        'data' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
