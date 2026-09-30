<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameProgress extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'activity_id',
        'stars_earned',
        'coins_earned',
    ];

    protected function casts(): array
    {
        return [
            'stars_earned' => 'integer',
            'coins_earned' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
