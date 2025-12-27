<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftItemsTable extends Migration
{
    public function up()
    {
        Schema::create('gift_items', function (Blueprint $table) {

            //## Basic fields - الحقول الأساسية
            $table->id();
            $table->string('code')->nullable()->unique(); //green Unique gift code - رمز الهدية الفريد
            $table->string('name'); //green Gift name - اسم الهدية
            $table->string('category')->nullable(); //green Gift category - فئة الهدية

            //## Inventory fields - حقول المخزون
            $table->string('unit')->nullable(); //green Unit of measurement - وحدة القياس ex: 'piece', 'box', 'set'
            $table->decimal('cost_per_unit', 12, 2)->nullable(); //green Cost per unit - التكلفة لكل وحدة
            $table->integer('total_stock')->default(0); //green Total stock quantity - الكمية الإجمالية في المخزون

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gift_items');
    }
}
