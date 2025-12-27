<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftDistributionsTable extends Migration
{
    public function up()
    {
        Schema::create('gift_distributions', function (Blueprint $table) {

            //## Basic fields - الحقول الأساسية
            $table->char('id', 36)->primary(); //green UUID primary key - معرف UUID
            $table->integer('quantity_distributed')->default(0); //green Distributed quantity - الكمية الموزعة
            $table->timestamp('distributed_at')->nullable(); //green Distribution date - تاريخ التوزيع
            $table->text('notes')->nullable(); //green Distribution notes - ملاحظات التوزيع

            $table->timestamps();

            //ref Foreign keys - مفاتيح خارجية
            $table->char('gift_allocation_id', 36); //green Gift allocation - تخصيص الهدية
            $table->unsignedBigInteger('client_id')->nullable(); //green Client receiving gift - العميل المستلم للهدية
            $table->unsignedBigInteger('created_by')->nullable(); //green Distribution creator - منشئ التوزيع

            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('gift_allocation_id')->references('id')->on('gift_allocations')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gift_distributions');
    }
}
