<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdAssetsTable extends Migration
{
    public function up()
    {
        Schema::create('ad_assets', function (Blueprint $table) {

            //## Basic fields - الحقول الأساسية
            $table->bigIncrements('id');
            $table->string('asset_code')->nullable()->unique(); //green Unique asset code - رمز الأصل الفريد
            $table->string('name'); //green Asset name - اسم الأصل
            $table->string('type')->nullable(); //green Asset type - نوع الأصل ex: 'banner', 'poster', 'display'
            $table->text('description')->nullable(); //green Asset description - وصف الأصل
            $table->json('specs')->nullable(); //green Technical specifications - المواصفات الفنية

            //## Inventory fields - حقول المخزون
            $table->integer('quantity')->default(0); //green Total quantity - الكمية الإجمالية
            $table->integer('used_quantity')->default(0); //green Used quantity - الكمية المستخدمة
            $table->decimal('cost_per_unit', 12, 2)->nullable(); //green Cost per unit - التكلفة لكل وحدة

            //## Status fields - حقول الحالة
            $table->enum('status', ['available', 'in_use', 'maintenance', 'retired'])->default('available'); //green Asset status - حالة الأصل
            $table->enum('condition', ['new', 'good', 'used', 'damaged'])->nullable(); //green Physical condition - الحالة الفعلية

            //## Location and ownership - الموقع والملكية
            $table->unsignedInteger('location_id')->nullable(); //green Current location - الموقع الحالي
            $table->unsignedInteger('supplier_id')->nullable(); //green Supplier - المورد

            //## Dates and media - التواريخ والوسائط
            $table->date('purchase_date')->nullable(); //green Purchase date - تاريخ الشراء
            $table->timestamp('last_used_at')->nullable(); //green Last usage date - تاريخ آخر استخدام
            $table->json('photos')->nullable(); //green Asset photos - صور الأصل

            $table->timestamps();
            $table->softDeletes(); //green Soft delete support - دعم الحذف الناعم

            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedBigInteger('ad_asset_category_id')->nullable(); //green Asset category - فئة الأصل
            $table->unsignedBigInteger('owner_branch_id')->nullable(); //green Owning branch - الفرع المالك
            $table->unsignedBigInteger('created_by')->nullable(); //green Creator user - المستخدم المنشئ

            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('ad_asset_category_id')->references('id')->on('ad_asset_categories')->onDelete('set null');
            $table->foreign('owner_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            //idx Indexes for performance - فهارس لتحسين الأداء
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_assets');
    }
}
