<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\DailyChallenge;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $ranking = User::with('profile')
            ->withSum('gameProgress', 'stars_earned')
            ->withSum('gameProgress', 'coins_earned')
            ->where('is_admin', false)
            ->get()
            ->sortByDesc(fn (User $user) => ($user->profile->base_stars ?? 0) + ($user->game_progress_sum_stars_earned ?? 0))
            ->values()
            ->map(fn (User $user, int $index) => [
                'position' => $index + 1,
                'username' => $user->username,
                'stars' => ($user->profile->base_stars ?? 0) + ($user->game_progress_sum_stars_earned ?? 0),
                'coins' => ($user->profile->base_coins ?? 0) + ($user->game_progress_sum_coins_earned ?? 0),
                'streak' => $user->profile->streak ?? 0,
            ]);

        return response()->json([
            'ranking' => $ranking,
            'subjects' => Subject::withCount('activities')->orderBy('sort_order')->get(),
            'activities' => Activity::with('subject')->latest()->get(),
            'dailyChallenges' => DailyChallenge::with('activity')->latest()->get(),
        ]);
    }

    public function storeSubject(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'], 'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'], 'story_arc' => ['nullable', 'string'],
            'difficulty' => ['nullable', 'string', 'max:30'], 'accent' => ['nullable', 'string', 'max:30'],
            'learning_goals' => ['nullable', 'array'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        $data['sort_order'] = (Subject::max('sort_order') ?? -1) + 1;
        $subject = Subject::create($data);

        return response()->json($subject, 201);
    }

    public function storeActivity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'], 'title' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:multiple-choice,target,collection,race,garden,sort,train,memory,matching'],
            'prompt' => ['required', 'string'], 'content' => ['nullable', 'array'], 'answer' => ['required'],
            'reward' => ['required', 'integer', 'min:1', 'max:100'], 'mechanic' => ['nullable', 'string'],
            'difficulty' => ['nullable', 'string', 'max:30'], 'tip' => ['nullable', 'string'],
        ]);
        $choiceTypes = ['multiple-choice', 'target', 'collection', 'race', 'garden'];
        if (in_array($data['type'], $choiceTypes, true)) {
            validator($data, ['content.options' => ['required', 'array', 'min:2']])->validate();
        } elseif (in_array($data['type'], ['sort', 'train'], true)) {
            validator($data, ['content.values' => ['required', 'array', 'min:2'], 'answer' => ['required', 'array', 'min:2']])->validate();
        } elseif ($data['type'] === 'memory') {
            validator($data, ['content.cards' => ['required', 'array', 'min:4'], 'answer' => ['required', 'array', 'min:4']])->validate();
        } else {
            validator($data, ['content.pairs' => ['required', 'array', 'min:2'], 'answer' => ['required', 'array', 'min:2']])->validate();
        }
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        $data['sort_order'] = (Activity::where('subject_id', $data['subject_id'])->max('sort_order') ?? -1) + 1;
        $activity = Activity::create($data);

        return response()->json($activity, 201);
    }

    public function storeChallenge(Request $request): JsonResponse
    {
        $data = $request->validate([
            'activity_id' => ['required', 'exists:activities,id'], 'title' => ['required', 'string', 'max:160'],
            'reward' => ['required', 'integer', 'min:1', 'max:100'], 'status' => ['required', 'in:disponible,bloqueado'],
            'mechanic' => ['nullable', 'string'], 'available_on' => ['nullable', 'date'],
        ]);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        $challenge = DailyChallenge::create($data);

        return response()->json($challenge, 201);
    }
}
