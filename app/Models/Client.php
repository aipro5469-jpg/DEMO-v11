<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Client extends Model
{
        use HasFactory, \Spatie\Permission\Traits\HasRoles;

        protected $fillable = [
                'name',
                'phone',
                'email',
                'category',
                'agent_id',
                'branch_id',
                'address',
                'gps_location',
                'images',
                'profile_image',
                'importance',
                'province',
                'district',
                'notes',
                'is_agent',
                'last_visit',
                'loyalty_level',
                'created_by',
                'parent_id',
                'type',
                'status',
                'classification_id',
                'images_inside',
                'images_outside',
                // New Fields
                'shop_name',
                'shop_phones',
                'owner_name',
                'owner_phones',
                'shop_size',
                'technician_count',
                'cooperation_level',
                'has_bias',
                'bias_company_name',
                'main_supplier_name',
                'supplier_satisfaction_rating',
                'competitor_goods_availability',
                'aljabali_goods_availability',
                'best_selling_item_aljabali',
                'best_selling_item_competitors',
                'quantity_sold',
                'positive_feedback',
                'negative_feedback',
                'consumer_complaints',
                'suggestions_new_parts',
                'suggestions_meters',
                'suggestions_unavailable',
                'suggestions_improvement',
                'workshop_type',
                'installed_spare_parts_types',
                'most_requested_parts',
                'technical_notes',
                'has_flange_oils',
                'most_used_oil_type',
                'oil_usage_reason',
                'oil_sales_increase_requirements',
                'opinion_aljabali_oils',
                'opinion_competitor_oils',
        ];

        protected $casts = [
                'is_agent' => 'boolean',
                // 'phone' => 'array', // Handled by accessor
                'images' => 'array',
                'images_inside' => 'array',
                'images_outside' => 'array',
                'last_visit' => 'datetime',
                'gps_location' => 'array',
                'shop_phones' => 'array',
                'owner_phones' => 'array',
                'installed_spare_parts_types' => 'array',
                'has_bias' => 'boolean',
                'has_flange_oils' => 'boolean',
                'technician_count' => 'integer',
                'supplier_satisfaction_rating' => 'integer',
                'quantity_sold' => 'integer',
        ];

        protected function phone(): \Illuminate\Database\Eloquent\Casts\Attribute
        {
                return \Illuminate\Database\Eloquent\Casts\Attribute::make(
                        get: function ($value) {
                                if (empty($value)) {
                                        return [];
                                }

                                // Try to decode JSON
                                $decoded = json_decode($value, true);

                                // If it's a valid JSON array, return it
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        // Ensure it's a flat array of strings
                                        return array_map(function ($item) {
                                                return is_array($item) ? ($item['number'] ?? '') : $item;
                                        }, $decoded);
                                }

                                // Otherwise, treat it as a legacy single phone number and wrap it
                                return [$value];
                        },
                        set: fn($value) => json_encode($value),
                );
        }

        // Relationships
        public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
        {
                return $this->belongsTo(User::class, 'created_by');
        }

        protected static function booted(): void
        {
                static::creating(function (Client $client) {
                        if (Auth::check()) {
                                $client->created_by = Auth::id();
                        }
                });
        }

        public function agent()
        {
                return $this->belongsTo(Agent::class);
        }

        public function branch()
        {
                return $this->belongsTo(Branch::class);
        }
        public function evaluations(): \Illuminate\Database\Eloquent\Relations\MorphMany
        {
                return $this->morphMany(Evaluation::class, 'evaluable');
        }

        public function parent()
        {
                return $this->belongsTo(Client::class, 'parent_id');
        }

        public function children()
        {
                return $this->hasMany(Client::class, 'parent_id');
        }

        public function employees()
        {
                return $this->hasMany(ClientEmployee::class);
        }
}
