<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'level', 'base_stars', 'base_coins', 'badges', 'age_group',
        'avatar', 'streak', 'last_activity_date', 'favorite_subject', 'last_quest',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer', 'base_stars' => 'integer', 'base_coins' => 'integer',
            'badges' => 'integer', 'streak' => 'integer', 'last_activity_date' => 'date',
        ];
    }
}