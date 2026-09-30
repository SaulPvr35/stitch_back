<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'price_cents', 'currency', 'interval', 'stripe_price_id', 'features', 'active'];

    protected function casts(): array
    {
        return ['price_cents' => 'integer', 'features' => 'array', 'active' => 'boolean'];
    }
}