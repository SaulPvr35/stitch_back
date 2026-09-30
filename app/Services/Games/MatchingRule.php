<?php

namespace App\Services\Games;

class MatchingRule implements GameRule
{
    public function passes(array $activity, mixed $answer): bool
    {
        if (!is_array($answer) || count($answer) !== count($activity['pairs'])) {
            return false;
        }

        $normalize = static fn (array $pair): string => json_encode([
            'left' => $pair['left'] ?? null,
            'right' => $pair['right'] ?? null,
        ]);

        $expected = array_map($normalize, $activity['pairs']);
        $received = array_map($normalize, $answer);
        sort($expected);
        sort($received);

        return $received === $expected;
    }
}