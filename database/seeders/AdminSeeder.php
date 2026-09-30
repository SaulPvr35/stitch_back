<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrador',
            'email' => 'admin@aventura.local',
            'password' => '12345678',
            'is_admin' => true,
        ]);

        $admin->profile()->firstOrCreate([], [
            'level' => 1,
            'base_stars' => 0,
            'base_coins' => 0,
            'avatar' => 'A',
            'age_group' => '8-9',
        ]);
    }
}