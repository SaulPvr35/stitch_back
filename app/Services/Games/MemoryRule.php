<?php

namespace App\Services\Games;

class MemoryRule implements GameRule
{
    public function passes(array $activity, mixed $answer): bool
    {
        if (!is_array($answer) || count($answer) !== count($activity['cards'])) {
            return false;
        }

        $expected = $activity['cards'];
        sort($expected);
        $received = $answer;
        sort($received);

        return $received === $expected;
    }
}