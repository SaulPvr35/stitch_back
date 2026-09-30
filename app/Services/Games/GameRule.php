<?php

namespace App\Services\Games;

interface GameRule
{
    public function passes(array $activity, mixed $answer): bool;
}