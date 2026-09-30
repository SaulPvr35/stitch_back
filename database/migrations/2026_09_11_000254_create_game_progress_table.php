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
        Schema::create('game_progress', function (Blueprint $table) {
            $table->id();
            $table->string('player_key')->default('alex');
            $table->string('subject', 32);
            $table->string('activity_id', 64);
            $table->unsignedInteger('stars_earned')->default(0);
            $table->unsignedInteger('coins_earned')->default(5);
            $table->timestamps();

            $table->unique(['player_key', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_progress');
    }
};
