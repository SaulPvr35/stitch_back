<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 60)->nullable()->unique()->after('name');
            $table->string('email')->nullable()->change();
        });

        DB::table('users')->whereNull('username')->orderBy('id')->each(function (object $user): void {
            DB::table('users')->where('id', $user->id)->update([
                'username' => 'explorador_'.$user->id,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
        });
    }
};