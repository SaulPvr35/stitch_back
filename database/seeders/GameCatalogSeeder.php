<?php

namespace Database\Seeders;

use App\Models\GameCatalog;
use Illuminate\Database\Seeder;

class GameCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $payload = json_decode(
            file_get_contents(database_path('game_catalog.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        GameCatalog::updateOrCreate(
            ['slug' => 'default'],
            ['payload' => $payload],
        );
    }
}