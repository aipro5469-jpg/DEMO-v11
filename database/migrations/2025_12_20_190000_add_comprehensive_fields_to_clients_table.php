<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // 1. General Data - Shop Info
            $table->string('shop_name')->nullable();
            $table->json('shop_phones')->nullable(); // Array of strings
            $table->string('owner_name')->nullable();
            $table->json('owner_phones')->nullable(); // Array of strings
            $table->string('shop_size')->nullable();
            $table->integer('technician_count')->nullable();

            // 2. Relationship & Cooperation
            $table->string('cooperation_level')->nullable();
            // importance is already in the table, but we might need to update enum values if different
            // $table->enum('importance', ['high', 'medium', 'low'])->nullable(); // Existing field
            $table->boolean('has_bias')->default(false);
            $table->string('bias_company_name')->nullable();

            // 3. Suppliers & Goods
            $table->string('main_supplier_name')->nullable();
            $table->integer('supplier_satisfaction_rating')->nullable(); // 1-5
            $table->string('competitor_goods_availability')->nullable();
            $table->string('aljabali_goods_availability')->nullable();

            // 4. Sales & Impressions
            $table->string('best_selling_item_aljabali')->nullable();
            $table->string('best_selling_item_competitors')->nullable();
            $table->integer('quantity_sold')->nullable(); // Optional

            // 5. Market Feedback
            $table->text('positive_feedback')->nullable();
            $table->text('negative_feedback')->nullable();
            $table->text('consumer_complaints')->nullable();

            // 6. Suggestions
            $table->text('suggestions_new_parts')->nullable();
            $table->text('suggestions_meters')->nullable();
            $table->text('suggestions_unavailable')->nullable();
            $table->text('suggestions_improvement')->nullable();

            // 7. Specific Data - Engineers
            $table->string('workshop_type')->nullable();
            $table->json('installed_spare_parts_types')->nullable(); // Array of strings
            $table->text('most_requested_parts')->nullable();
            $table->text('technical_notes')->nullable();

            // 8. Specific Data - Oil Traders
            $table->boolean('has_flange_oils')->default(false);
            $table->string('most_used_oil_type')->nullable();
            $table->string('oil_usage_reason')->nullable();
            $table->text('oil_sales_increase_requirements')->nullable();
            $table->text('opinion_aljabali_oils')->nullable();
            $table->text('opinion_competitor_oils')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
