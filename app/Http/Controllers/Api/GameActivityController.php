<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\GameProgressService;
use App\Services\Games\GameRuleResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameActivityController extends Controller
{
    public function __construct(private readonly GameProgressService $progress)
    {
    }

    public function submit(Request $request, string $activityId): JsonResponse
    {
        $data = $request->validate(['answer' => ['required']]);

            $activityModel = Activity::with('subject')->where('slug', $activityId)->where('active', true)->first();
            $activity = $activityModel ? [
                'id' => $activityModel->slug,
                'subject' => $activityModel->subject->slug,
                'type' => $activityModel->type,
                'answer' => $activityModel->answer,
                'cards' => $activityModel->content['cards'] ?? null,
                'pairs' => $activityModel->content['pairs'] ?? null,
                'reward' => $activityModel->reward,
                'title' => $activityModel->title,
            ] : null;

        abort_if($activity === null, 404, 'Activity not found.');

        $isCorrect = GameRuleResolver::for($activity['type'])->passes($activity, $data['answer']);

        if (!$isCorrect) {
            return response()->json([
                'correct' => false,
                'message' => 'La respuesta no cumple las reglas de esta actividad.',
            ], 422);
        }

        return response()->json([
            'correct' => true,
            'progress' => $this->progress->complete($request->user()->loadMissing('profile'), $activity),
        ]);
    }
}