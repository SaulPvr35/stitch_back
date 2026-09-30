<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email');
            $table->string('password');
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};