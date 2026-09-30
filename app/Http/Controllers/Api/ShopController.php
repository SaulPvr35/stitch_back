<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopItem;
use App\Services\GameProgressService;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopService $shop,
        private readonly GameProgressService $progress,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->shop->catalogFor($request->user()->loadMissing('profile')));
    }

    public function purchase(Request $request, ShopItem $shopItem): JsonResponse
    {
        try {
            $user = $request->user()->loadMissing('profile');
            $shopState = $this->shop->purchase($user, $shopItem);

            return response()->json([
                'message' => 'Artículo añadido a tu colección.',
                'shop' => $shopState,
                'progress' => $this->progress->stateFor($user),
            ]);
        } catch (RuntimeException $error) {
            return response()->json(['message' => $error->getMessage()], 422);
        }
    }

    public function toggleEquipped(Request $request, ShopItem $shopItem): JsonResponse
    {
        try {
            $user = $request->user()->loadMissing('profile');

            return response()->json([
                'shop' => $this->shop->toggleEquipped($user, $shopItem),
            ]);
        } catch (RuntimeException $error) {
            return response()->json(['message' => $error->getMessage()], 422);
        }
    }
}
