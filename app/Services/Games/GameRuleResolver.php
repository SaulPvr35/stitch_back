<?php

namespace App\Services\Games;

use InvalidArgumentException;

class GameRuleResolver
{
    public static function for(string $type): GameRule
    {
        return match ($type) {
            'multiple-choice', 'target', 'collection', 'race', 'garden' => new MultipleChoiceRule(),
            'sort', 'train' => new SortRule(),
            'memory' => new MemoryRule(),
            'matching' => new MatchingRule(),
            default => throw new InvalidArgumentException("Unsupported game type: {$type}"),
        };
    }
}