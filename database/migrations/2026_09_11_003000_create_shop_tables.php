<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('category', 32);
            $table->text('description');
            $table->unsignedInteger('price');
            $table->string('icon', 32)->default('star');
            $table->json('metadata')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_shop_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_item_id')->constrained()->cascadeOnDelete();
            $table->timestamp('purchased_at');
            $table->unique(['user_id', 'shop_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_shop_items');
        Schema::dropIfExists('shop_items');
    }
};