<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameCatalog extends Model
{
    protected $table = 'game_catalog';

    protected $fillable = ['slug', 'payload'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}