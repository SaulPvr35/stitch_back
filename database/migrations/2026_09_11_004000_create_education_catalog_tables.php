<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('name');
            $table->string('title');
            $table->string('short_name')->nullable();
            $table->string('color', 30)->nullable();
            $table->string('accent', 30)->nullable();
            $table->text('description')->nullable();
            $table->text('story_arc')->nullable();
            $table->json('learning_goals')->nullable();
            $table->string('difficulty', 30)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 80)->unique();
            $table->string('title');
            $table->string('type', 40);
            $table->string('mechanic')->nullable();
            $table->text('prompt');
            $table->json('content')->nullable();
            $table->json('answer')->nullable();
            $table->unsignedInteger('reward')->default(10);
            $table->string('difficulty', 30)->nullable();
            $table->text('tip')->nullable();
            $table->string('story_step')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('daily_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 80)->unique();
            $table->string('title');
            $table->unsignedInteger('reward');
            $table->string('status', 30)->default('disponible');
            $table->string('mechanic')->nullable();
            $table->date('available_on')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_challenges');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('subjects');
    }
};