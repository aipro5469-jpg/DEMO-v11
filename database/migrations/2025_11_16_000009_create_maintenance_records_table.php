<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            
            //## Basic fields - الحقول الأساسية
            $table->bigIncrements('id');
            $table->enum('maintenance_type', ['repair', 'clean', 'upgrade'])->nullable(); //green Type of maintenance - نوع الصيانة
            $table->decimal('cost', 12, 2)->nullable(); //green Maintenance cost - تكلفة الصيانة
            $table->text('notes')->nullable(); //green Maintenance notes - ملاحظات الصيانة
            $table->timestamp('performed_at')->nullable(); //green Maintenance date - تاريخ الصيانة
            
            $table->timestamps();
            
            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedBigInteger('asset_id'); //green Asset being maintained - الأصل الذي تتم صيانته
            $table->unsignedBigInteger('performed_by')->nullable(); //green User who performed maintenance - المستخدم الذي قام بالصيانة
            
            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('asset_id')->references('id')->on('ad_assets')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_records');
    }
}
