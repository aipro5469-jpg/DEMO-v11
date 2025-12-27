<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('evaluation_templates', function (Blueprint $table) {
            
            //## Basic fields - الحقول الأساسية
            $table->increments('id');
            $table->string('name'); //green Template name - اسم القالب
            $table->string('evaluable_type')->nullable(); //green Type of entity being evaluated - نوع الكيان المُقيّم
            $table->text('description')->nullable(); //green Template description - وصف القالب
            $table->boolean('is_active')->default(true); //green Is template active - هل القالب نشط
            
            $table->timestamps();
            $table->softDeletes(); //green Soft delete support - دعم الحذف الناعم
            
            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedBigInteger('created_by')->nullable(); //green Template creator - منشئ القالب
            
            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_templates');
    }
}
