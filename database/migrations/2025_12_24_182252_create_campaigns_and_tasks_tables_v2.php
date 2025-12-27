<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // field, phone, digital, sales, ads
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, active, paused, completed
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->unsignedBigInteger('creator_id');
            // $table->foreign('creator_id')->references('id')->on('users')->cascadeOnDelete();

            $table->boolean('is_template')->default(false);
            $table->json('template_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_tasks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('campaign_id')->nullable();
            // $table->foreign('campaign_id')->references('id')->on('marketing_campaigns')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->dateTime('due_date')->nullable();
            $table->string('status')->default('todo'); // todo, in_progress, done
            $table->integer('progress')->default(0);

            $table->unsignedBigInteger('assigned_to')->nullable();
            // $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('marketing_task_checkpoints', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('task_id');
            // $table->foreign('task_id')->references('id')->on('marketing_tasks')->cascadeOnDelete();

            $table->string('title');
            $table->string('type')->default('checkbox'); // checkbox, text, photo, measurement, location
            $table->boolean('is_required')->default(false);
            $table->string('status')->default('pending'); // pending, completed
            $table->timestamp('completed_at')->nullable();

            $table->unsignedBigInteger('completed_by')->nullable();
            // $table->foreign('completed_by')->references('id')->on('users')->nullOnDelete();

            $table->json('data')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_activities', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->string('type'); // campaign_created, task_completed, etc.
            $table->nullableMorphs('subject');
            $table->text('description')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_activities');
        Schema::dropIfExists('marketing_task_checkpoints');
        Schema::dropIfExists('marketing_tasks');
        Schema::dropIfExists('marketing_campaigns');
    }
};
