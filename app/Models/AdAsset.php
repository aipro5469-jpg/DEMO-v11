<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AdAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_code',
        'name',
        'ad_asset_category_id',
        'type',
        'description',
        'specs',
        'cost_per_unit',
        'quantity',
        'used_quantity',
        'status',
        'condition',
        'location_id',
        'owner_branch_id',
        'supplier_id',
        'purchase_date',
        'last_used_at',
        'photos',
        'created_by',
    ];

    protected $casts = [
        'specs' => 'array',
        'photos' => 'array',
        'purchase_date' => 'date',
        'last_used_at' => 'datetime',
        'cost_per_unit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->asset_code)) {
                $model->asset_code = 'AST-' . strtoupper(Str::random(8));
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(AdAssetCategory::class, 'ad_asset_category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'owner_branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
