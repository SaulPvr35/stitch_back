<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\DailyChallenge;
use App\Models\GameCatalog;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class EducationCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = GameCatalog::where('slug', 'default')->firstOrFail()->payload;

        foreach ($catalog['subjects'] as $index => $item) {
            $subject = Subject::updateOrCreate(
                ['slug' => $item['id']],
                [
                    'name' => $item['name'], 'title' => $item['title'], 'short_name' => $item['shortName'] ?? null,
                    'color' => $item['color'] ?? null, 'accent' => $item['accent'] ?? null,
                    'description' => $item['description'] ?? null, 'story_arc' => $item['storyArc'] ?? null,
                    'learning_goals' => $item['learningGoals'] ?? [], 'difficulty' => $item['difficulty'] ?? null,
                    'sort_order' => $index, 'active' => true,
                ],
            );

            foreach ($catalog['activities'][$item['id']] ?? [] as $activityIndex => $activity) {
                $content = array_filter([
                    'options' => $activity['options'] ?? null,
                    'values' => $activity['values'] ?? null,
                    'cards' => $activity['cards'] ?? null,
                    'pairs' => $activity['pairs'] ?? null,
                ], static fn ($value) => $value !== null);

                Activity::updateOrCreate(
                    ['slug' => $activity['id']],
                    [
                        'subject_id' => $subject->id, 'title' => $activity['title'], 'type' => $activity['type'],
                        'mechanic' => $activity['mechanic'] ?? null, 'prompt' => $activity['prompt'],
                        'content' => $content, 'answer' => $activity['answer'] ?? null, 'reward' => $activity['reward'] ?? 10,
                        'difficulty' => $activity['difficulty'] ?? null, 'tip' => $activity['tip'] ?? null,
                        'story_step' => $activity['storyStep'] ?? null, 'sort_order' => $activityIndex, 'active' => true,
                    ],
                );
            }
        }

        foreach ($catalog['dailyChallenges'] as $challenge) {
            $activity = Activity::where('slug', $challenge['activityId'])->first();
            if ($activity) {
                DailyChallenge::updateOrCreate(
                    ['slug' => $challenge['id']],
                    [
                        'activity_id' => $activity->id, 'title' => $challenge['title'], 'reward' => $challenge['reward'],
                        'status' => $challenge['status'], 'mechanic' => $challenge['mechanic'] ?? null,
                    ],
                );
            }
        }

        User::where('username', 'admin')->update(['is_admin' => true]);
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            ['name' => 'Administrador', 'password' => Hash::make('admin123'), 'is_admin' => true],
        );
        $admin->update(['name' => 'Administrador', 'password' => Hash::make('admin123'), 'is_admin' => true]);
        $admin->profile()->firstOrCreate([], ['avatar' => 'A']);

        $player = User::firstOrCreate(
            ['username' => 'saul'],
            ['name' => 'Saul', 'password' => Hash::make('12345678'), 'is_admin' => false],
        );
        $player->update(['name' => 'Saul', 'password' => Hash::make('12345678'), 'is_admin' => false]);
        $player->profile()->firstOrCreate([], [
            'avatar' => 'S',
            'age_group' => '8-9',
        ]);
    }
}