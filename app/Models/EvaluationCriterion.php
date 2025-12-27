<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationCriterion extends Model
{
    protected $table = 'evaluation_criteria';

    protected $fillable = [
        'evaluation_template_id',
        'key',
        'name',
        'description',
        'weight',
        'min_value',
        'max_value',
        'order',
    ];

    protected $casts = [
        'weight' => 'integer',
        'min_value' => 'integer',
        'max_value' => 'integer',
        'order' => 'integer',
    ];

    // Relationships
    public function template()
    {
        return $this->belongsTo(EvaluationTemplate::class, 'evaluation_template_id');
    }
}
