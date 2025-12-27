<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdUsageLogsTable extends Migration
{
    public function up()
    {
        Schema::create('ad_usage_logs', function (Blueprint $table) {
            
            //## Basic fields - الحقول الأساسية
            $table->bigIncrements('id');
            $table->string('location')->nullable(); //green Usage location - موقع الاستخدام
            $table->timestamp('used_at')->nullable(); //green Usage date - تاريخ الاستخدام
            $table->text('notes')->nullable(); //green Usage notes - ملاحظات الاستخدام
            $table->json('photos')->nullable(); //green Usage photos - صور الاستخدام
            
            $table->timestamps();
            
            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedBigInteger('asset_id'); //green Asset being used - الأصل المستخدم
            $table->char('campaign_id', 36)->nullable(); //green Associated campaign - الحملة المرتبطة
            $table->unsignedBigInteger('assigned_to')->nullable(); //green User assigned to use asset - المستخدم المعين لاستخدام الأصل
            $table->unsignedBigInteger('created_by')->nullable(); //green Log creator - منشئ السجل
            
            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('asset_id')->references('id')->on('ad_assets')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_usage_logs');
    }
}
