<?php

namespace App\Services;

use App\Models\GameProgress;
use App\Models\User;
use Carbon\CarbonImmutable;

class GameProgressService
{
    public function __construct(private readonly ShopService $shop)
    {
    }

    public function stateFor(User $user): array
    {
        $completed = GameProgress::query()
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('subject')
            ->map(fn ($items) => $items->pluck('activity_id')->values())
            ->all();

        return [
            'completed' => array_merge([
                'math' => [],
                'spanish' => [],
                'science' => [],
            ], $completed),
            'stars' => $user->profile->base_stars + GameProgress::where('user_id', $user->id)->sum('stars_earned'),
            'coins' => $this->shop->balance($user),
            'streak' => $user->profile->streak,
        ];
    }

    public function complete(User $user, array $activity): array
    {
        $progress = GameProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'activity_id' => $activity['id'],
            ],
            [
                'subject' => $activity['subject'],
                'stars_earned' => $activity['reward'],
                'coins_earned' => 5,
            ],
        );

        if ($progress->wasRecentlyCreated) {
            $today = CarbonImmutable::today();
            $lastActivity = $user->profile->last_activity_date;
            $yesterday = $today->subDay()->toDateString();
            $lastActivityDate = $lastActivity?->toDateString();

            $user->profile->update([
                'streak' => $lastActivityDate === $yesterday
                    ? $user->profile->streak + 1
                    : ($lastActivityDate === $today->toDateString() ? $user->profile->streak : 1),
                'last_activity_date' => $today->toDateString(),
                'last_quest' => $activity['title'],
            ]);
        }

        return $this->stateFor($user);
    }
}