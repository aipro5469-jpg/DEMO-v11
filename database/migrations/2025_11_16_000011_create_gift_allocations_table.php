<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('gift_allocations', function (Blueprint $table) {

            //## Basic fields - الحقول الأساسية
            $table->char('id', 36)->primary(); //green UUID primary key - معرف UUID
            $table->integer('quantity_allocated')->default(0); //green Allocated quantity - الكمية المخصصة
            $table->timestamp('allocated_at')->nullable(); //green Allocation date - تاريخ التخصيص
            $table->text('notes')->nullable(); //green Allocation notes - ملاحظات التخصيص

            $table->timestamps();

            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedBigInteger('gift_id'); //green Gift item - عنصر الهدية
            $table->unsignedBigInteger('marketer_id')->nullable(); //green Marketer receiving allocation - المسوق المستلم للتخصيص
            $table->unsignedBigInteger('created_by')->nullable(); //green Allocation creator - منشئ التخصيص

            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('gift_id')->references('id')->on('gift_items')->onDelete('cascade');
            $table->foreign('marketer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gift_allocations');
    }
}
