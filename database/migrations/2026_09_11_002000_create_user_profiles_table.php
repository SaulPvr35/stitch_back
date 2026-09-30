<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('level')->default(1);
            $table->unsignedInteger('base_stars')->default(0);
            $table->unsignedInteger('base_coins')->default(0);
            $table->unsignedInteger('badges')->default(0);
            $table->string('age_group', 20)->nullable();
            $table->string('avatar', 10)->default('A');
            $table->unsignedInteger('streak')->default(0);
            $table->string('favorite_subject', 32)->nullable();
            $table->string('last_quest')->nullable();
            $table->timestamps();
        });

        DB::table('users')->orderBy('id')->each(function (object $user): void {
            DB::table('user_profiles')->insert([
                'user_id' => $user->id,
                'avatar' => strtoupper(substr($user->name, 0, 1)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};