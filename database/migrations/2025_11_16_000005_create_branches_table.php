<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
/**/
            // Basic fields - الحقول الأساسية
            //## Basic fields - الحقول الأساسية
            $table->id();
            $table->string('name'); //green Branch name - اسم الفرع
            $table->string('code')->nullable()->unique(); //green Unique branch code - رمز الفرع الفريد
            $table->string('location')->nullable(); //green Branch location - موقع الفرع
            $table->boolean('is_active')->default(true); //green Active status - حالة النشاط

            $table->timestamps();
            $table->softDeletes(); //green Soft delete support - دعم الحذف الناعم
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
