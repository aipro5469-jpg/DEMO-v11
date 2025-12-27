<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationCriteriaTable extends Migration
{
    public function up()
    {
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            
            //## Basic fields - الحقول الأساسية
            $table->increments('id');
            $table->string('key'); //green Criterion key - مفتاح المعيار
            $table->string('name'); //green Criterion name - اسم المعيار
            $table->text('description')->nullable(); //green Criterion description - وصف المعيار
            
            //## Scoring fields - حقول التقييم
            $table->integer('weight')->default(1); //green Criterion weight - وزن المعيار
            $table->integer('min_value')->default(1); //green Minimum score value - أقل قيمة للتقييم
            $table->integer('max_value')->default(5); //green Maximum score value - أعلى قيمة للتقييم
            $table->integer('order')->default(0); //green Display order - ترتيب العرض
            
            $table->timestamps();
            
            //ref Foreign keys - مفاتيح خارجية
            $table->unsignedInteger('evaluation_template_id'); //green Template this criterion belongs to - القالب الذي ينتمي إليه هذا المعيار
            
            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('evaluation_template_id')->references('id')->on('evaluation_templates')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_criteria');
    }
}
