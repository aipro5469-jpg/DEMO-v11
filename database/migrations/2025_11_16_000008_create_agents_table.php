<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {

            //## Basic fields - الحقول الأساسية
            $table->id();
            $table->string('name'); //green Agent name - اسم الوكيل
            $table->string('phone')->nullable(); //green Phone number - رقم الهاتف
            $table->string('email')->nullable(); //green Email - البريد الإلكتروني
            $table->string('region')->nullable(); //green Region - المنطقة
            $table->boolean('is_active')->default(true); //green Active status - حالة النشاط

            $table->timestamps();

            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedBigInteger('branch_id')->nullable(); //green Associated branch - الفرع المرتبط
            $table->unsignedBigInteger('user_id')->nullable(); //green Associated user - المستخدم المرتبط

            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agents');
    }
}
