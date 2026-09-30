<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['slug', 'name', 'title', 'short_name', 'color', 'accent', 'description', 'story_arc', 'learning_goals', 'difficulty', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['learning_goals' => 'array', 'active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('sort_order');
    }
}