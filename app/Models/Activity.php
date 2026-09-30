<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['subject_id', 'slug', 'title', 'type', 'mechanic', 'prompt', 'content', 'answer', 'reward', 'difficulty', 'tip', 'story_step', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['content' => 'array', 'answer' => 'array', 'active' => 'boolean', 'reward' => 'integer', 'sort_order' => 'integer'];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}