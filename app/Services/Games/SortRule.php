<?php

namespace App\Services\Games;

class SortRule implements GameRule
{
    public function passes(array $activity, mixed $answer): bool
    {
        return is_array($answer) && $answer === $activity['answer'];
    }
}