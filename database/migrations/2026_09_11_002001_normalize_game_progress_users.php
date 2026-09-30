<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_progress', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        $legacyUser = DB::table('users')->where('email', 'test@example.com')->value('id');
        if ($legacyUser) {
            DB::table('game_progress')->where('player_key', 'alex')->update(['user_id' => $legacyUser]);
        }

        Schema::table('game_progress', function (Blueprint $table) {
            $table->dropUnique('game_progress_player_key_activity_id_unique');
            $table->unique(['user_id', 'activity_id']);
            $table->dropColumn('player_key');
        });
    }

    public function down(): void
    {
        Schema::table('game_progress', function (Blueprint $table) {
            $table->string('player_key')->default('alex')->after('id');
            $table->dropUnique('game_progress_user_id_activity_id_unique');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unique(['player_key', 'activity_id']);
        });
    }
};