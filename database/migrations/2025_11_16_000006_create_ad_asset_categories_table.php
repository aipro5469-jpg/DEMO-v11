<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdAssetCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('ad_asset_categories', function (Blueprint $table) {

            //## Basic fields - الحقول الأساسية
            $table->id();
            $table->string('name'); //green Category name - اسم الفئة
            $table->text('description')->nullable(); //green Category description - وصف الفئة

            $table->timestamps();

            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedBigInteger('parent_id')->nullable(); //green Parent category for hierarchy - الفئة الأم للتسلسل الهرمي

            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('parent_id')->references('id')->on('ad_asset_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_asset_categories');
    }
}
