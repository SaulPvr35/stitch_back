<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserShopItem extends Model
{
    public $timestamps = false;

    protected $table = 'user_shop_items';

    protected $fillable = ['user_id', 'shop_item_id', 'purchased_at', 'equipped'];

    protected function casts(): array
    {
        return ['purchased_at' => 'datetime', 'equipped' => 'boolean'];
    }
}