<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    protected $fillable = ['slug', 'name', 'category', 'description', 'price', 'icon', 'metadata', 'active'];

    protected function casts(): array
    {
        return ['price' => 'integer', 'metadata' => 'array', 'active' => 'boolean'];
    }
}