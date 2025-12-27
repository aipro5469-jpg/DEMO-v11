<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'marketing_campaigns';

    protected $fillable = [
        'name',
        'type',
        'description',
        'status',
        'start_date',
        'end_date',
        'creator_id',
        'is_template',
        'template_data',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_template' => 'boolean',
        'template_data' => 'array',
        'metadata' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'campaign_id');
    }

    public function updateProgress(): void
    {
        $totalTasks = $this->tasks()->count();

        if ($totalTasks === 0) {
            $this->update(['progress' => 0]);
            return;
        }

        // Calculate average progress of all tasks
        $totalProgress = $this->tasks()->sum('progress');
        $averageProgress = (int) round($totalProgress / $totalTasks);

        $this->update(['progress' => $averageProgress]);

        // Update status based on progress
        if ($averageProgress === 100 && $this->status !== 'completed') {
            $this->update(['status' => 'completed']);
        } elseif ($averageProgress > 0 && $averageProgress < 100 && $this->status === 'draft') {
            $this->update(['status' => 'active']);
        }
    }
}
