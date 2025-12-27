<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'evaluation_template_id',
        'evaluable_type',
        'evaluable_id',
        'evaluator_id',
        'scores',
        'total_score',
        'weighted_score',
        'notes',
        'evaluated_at',
    ];

    protected $casts = [
        'scores' => 'array',
        'total_score' => 'decimal:2',
        'weighted_score' => 'decimal:2',
        'evaluated_at' => 'datetime',
    ];

    // Relationships
    public function template()
    {
        return $this->belongsTo(EvaluationTemplate::class, 'evaluation_template_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluable()
    {
        return $this->morphTo();
    }
}
