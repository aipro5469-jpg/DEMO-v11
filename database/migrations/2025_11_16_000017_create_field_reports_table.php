<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldReportsTable extends Migration
{
    public function up()
    {
        Schema::create('field_reports', function (Blueprint $table) {
            
            //## Basic fields - الحقول الأساسية
            $table->char('id', 36)->primary(); //green UUID primary key - معرف UUID
            $table->text('notes')->nullable(); //green Report notes - ملاحظات التقرير
            
            //## Media and location - الوسائط والموقع
            $table->json('photos')->nullable(); //green Report photos - صور التقرير
            $table->string('location')->nullable(); //green Report location - موقع التقرير
            
            //## Sync fields - حقول المزامنة
            $table->enum('sync_status', ['pending', 'synced', 'conflict'])->default('pending'); //green Sync status - حالة المزامنة
            
            $table->timestamps();
            $table->softDeletes(); //green Soft delete support - دعم الحذف الناعم
            
            //ref Foreign keys - مفاتيح خارجية
            $table->char('task_id', 36)->nullable(); //green Associated task - المهمة المرتبطة
            $table->unsignedBigInteger('reporter_id')->nullable(); //green Report creator - منشئ التقرير
            
            //ref Foreign key relations - علاقات المفتاح الخارجي
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('set null');
            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('set null');
            
            //idx Indexes for performance - فهارس لتحسين الأداء
            $table->index(['task_id', 'reporter_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('field_reports');
    }
}
