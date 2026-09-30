<?php

namespace App\Services\Games;

class MultipleChoiceRule implements GameRule
{
    public function passes(array $activity, mixed $answer): bool
    {
        return $answer === $activity['answer'];
    }
}