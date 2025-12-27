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
            $table->foreignId('parent_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('type')->nullable(); // Merchant, Workshop, Distributor, etc.
            $table->string('status')->default('active'); // Active, Cold, Lost
            $table->unsignedBigInteger('classification_id')->nullable();
            $table->index('classification_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'type', 'status', 'classification_id']);
        });
    }
};
