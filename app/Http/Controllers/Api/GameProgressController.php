<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\DailyChallenge;
use App\Models\GameCatalog;
use App\Models\User;
use App\Services\GameProgressService;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameProgressController extends Controller
{
    public function __construct(
        private readonly GameProgressService $progress,
        private readonly ShopService $shop,
    )
    {
    }

    public function gameState(Request $request): JsonResponse
    {
        $subjectsData = Subject::with('activities')->where('active', true)->orderBy('sort_order')->get();
        $user = $request->user()->loadMissing('profile');

        $progress = $this->progress->stateFor($user);
        $subjects = $subjectsData->map(function (Subject $subject) use ($progress): array {
            $activities = $subject->activities->where('active', true)->values();

            return [
                'id' => $subject->slug, 'name' => $subject->name, 'title' => $subject->title,
                'shortName' => $subject->short_name, 'color' => $subject->color, 'accent' => $subject->accent,
                'description' => $subject->description, 'storyArc' => $subject->story_arc,
                'learningGoals' => $subject->learning_goals, 'difficulty' => $subject->difficulty,
                'progress' => count($progress['completed'][$subject->slug] ?? []),
                'total' => count($activities),
            ];
        })->values();
        $activities = $subjectsData->mapWithKeys(fn (Subject $subject) => [
            $subject->slug => $subject->activities->where('active', true)->map(fn ($activity) => $this->activityPayload($activity))->values(),
        ]);
        $userPayload = [
            'name' => $user->name,
            'username' => $user->username,
            'isAdmin' => $user->is_admin,
            'level' => $user->profile->level,
            'badges' => $user->profile->badges,
            'avatar' => $user->profile->avatar,
            'streak' => $user->profile->streak,
            'favoriteSubject' => $user->profile->favorite_subject,
            'lastQuest' => $user->profile->last_quest,
        ];

        $legacyCatalog = GameCatalog::where('slug', 'default')->first()?->payload ?? [];

        return response()->json([
            'user' => $userPayload,
            'subjects' => $subjects,
            'activities' => $activities,
            'dailyChallenges' => DailyChallenge::with('activity')->get()->map(fn ($challenge) => [
                'id' => $challenge->slug, 'title' => $challenge->title, 'subject' => $challenge->activity->subject->slug,
                'reward' => $challenge->reward, 'status' => $user->is_admin ? 'disponible' : $challenge->status, 'mechanic' => $challenge->mechanic,
                'activityId' => $challenge->activity->slug,
            ]),
            'journeyMap' => $subjects->map(fn ($subject) => ['id' => $subject['id'], 'label' => $subject['shortName'], 'unlocked' => true]),
            'achievements' => $legacyCatalog['achievements'] ?? [],
            'progress' => $progress,
            'shop' => $this->shop->catalogFor($user),
        ]);
    }

    private function activityPayload($activity): array
    {
        return [
            'id' => $activity->slug, 'title' => $activity->title, 'type' => $activity->type,
            'mechanic' => $activity->mechanic, 'prompt' => $activity->prompt,
                ...($activity->content ?? []), 'answer' => $activity->answer, 'reward' => $activity->reward,
            'subject' => $activity->subject->slug, 'difficulty' => $activity->difficulty,
            'tip' => $activity->tip, 'storyStep' => $activity->story_step,
        ];
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json($this->progress->stateFor($request->user()->loadMissing('profile')));
    }
}
