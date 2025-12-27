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
        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('branches')->nullOnDeleete();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->text('address')->nullable();
            $table->json('gps_location')->nullable();
            $table->string('manager_name')->nullable();
            $table->json('manager_phone')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->index('city_id');
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'parent_id',
                'city_id',
                'address',
                'gps_location',
                'manager_name',
                'manager_phone',
                'created_by'
            ]);
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
