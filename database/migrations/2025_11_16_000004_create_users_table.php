<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            
            //## Basic fields - الحقول الأساسية
            $table->bigIncrements('id');
            $table->char('uuid', 36)->unique()->default(DB::raw('(UUID())')); //green Unique identifier for sync - معرف فريد للمزامنة
            $table->string('name'); //green Full name - الاسم الكامل
            $table->string('username')->nullable()->unique(); //green Username for login - اسم المستخدم لتسجيل الدخول
            $table->string('email')->nullable()->unique(); //green Email address - البريد الإلكتروني
            $table->string('phone')->nullable(); //green Phone number - رقم الهاتف
            $table->string('password')->nullable(); //green Hashed password - كلمة المرور المشفرة
            
            //## User settings - إعدادات المستخدم
            $table->json('preferences')->nullable(); //green User preferences/settings - تفضيلات/إعدادات المستخدم
            $table->boolean('is_active')->default(true); //green Account active status - حالة نشاط الحساب
            
            $table->timestamps();
            $table->softDeletes(); //green Soft delete support - دعم الحذف الناعم
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
