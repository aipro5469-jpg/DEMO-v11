<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categories Table (Hierarchical)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->integer('level')->default(1);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        // Insert default 'Other' category
        DB::table('categories')->insert([
            'name' => 'أخرى',
            'code' => 'OTHER',
            'parent_id' => null,
            'level' => 1,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Items Table (Unified for Gifts/Sales)
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('item_type')->default('gift'); // gift, sale
            $table->string('unit')->nullable(); // piece, box, etc.
            $table->integer('total_stock')->default(0); // Cached total stock
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 3. Stock Movements Table (Global Stock History)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('type'); // in, out, return, adjustment
            $table->integer('quantity'); // Positive for IN/RETURN, Negative for OUT

            // Source (Where it came from)
            $table->nullableMorphs('source'); // e.g., App\Models\User (Warehouse/Marketer)

            // Destination (Where it went)
            $table->nullableMorphs('destination'); // e.g., App\Models\Client, App\Models\User

            $table->nullableMorphs('reference'); // Polymorphic relation to source (e.g., Assignment, Order)
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // 4. Inventory Assignments Table (Marketer Custody)
        Schema::create('inventory_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('quantity')->default(0); // Current quantity held by marketer
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['marketer_id', 'item_id']);
        });

        // 5. Inventory Distributions Table (Client Distribution)
        Schema::create('inventory_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketer_id')->constrained('users');
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity');
            $table->timestamp('distributed_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_distributions');
        Schema::dropIfExists('inventory_assignments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('items');
        Schema::dropIfExists('categories');
    }
};
