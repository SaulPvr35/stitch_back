<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyChallenge extends Model
{
    protected $fillable = ['activity_id', 'slug', 'title', 'reward', 'status', 'mechanic', 'available_on'];

    protected function casts(): array
    {
        return ['reward' => 'integer', 'available_on' => 'date'];
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}