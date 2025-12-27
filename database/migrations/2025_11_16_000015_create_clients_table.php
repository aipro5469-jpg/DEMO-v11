<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {

            //## Basic fields - الحقول الأساسية
            $table->id();
            $table->string('name');
            $table->json('phone')->nullable(); //green Multiple phone numbers - أرقام هواتف متعددة
            $table->string('email')->nullable();
            $table->string('category')->nullable(); //green Client category - فئة العميل ex: 'premium', 'standard', 'basic'

            //## Location fields - حقول الموقع
            $table->text('address')->nullable(); //green Physical address - العنوان الفعلي
            $table->string('gps_location')->nullable(); //green GPS coordinates - إحداثيات GPS
            $table->string('province')->nullable(); //green Province/Governorate - المحافظة
            $table->string('district')->nullable(); //green District - المنطقة/الحي

            //## Media fields - حقول الوسائط
            $table->json('images')->nullable(); //green Shop/location images - صور المحل/الموقع
            $table->string('profile_image')->nullable(); //green Client profile image - صورة العميل الشخصية

            //## Client status fields - حقول حالة العميل
            $table->string('importance')->nullable(); //green Client importance level - مستوى أهمية العميل ex: 'high', 'medium', 'low'
            $table->string('loyalty_level')->nullable(); //green Loyalty level - مستوى الولاء ex: 'gold', 'silver', 'bronze'
            $table->boolean('is_agent')->default(false); //green Is this client also an agent? - هل هذا العميل وكيل أيضاً؟
            $table->timestamp('last_visit')->nullable(); //green Last visit date - تاريخ آخر زيارة

            //## Additional information - معلومات إضافية
            $table->text('notes')->nullable(); //green General notes - ملاحظات عامة

            $table->timestamps();

            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedBigInteger('agent_id')->nullable(); //green Assigned agent - الوكيل المعين
            $table->unsignedBigInteger('branch_id')->nullable(); //green Associated branch - الفرع المرتبط

            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');

            //idx Indexes for performance - فهارس لتحسين الأداء
            $table->index(['branch_id', 'agent_id']);
            $table->index('category');
            $table->index('is_agent');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
