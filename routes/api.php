<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameProgressController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\AdminController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/auth/checkout/complete', [AuthController::class, 'completeCheckout']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/auth/me', [AuthController::class, 'me']);
	Route::post('/auth/logout', [AuthController::class, 'logout']);
	Route::get('/progress', [GameProgressController::class, 'show']);
	Route::get('/game-state', [GameProgressController::class, 'gameState']);
	Route::post('/activities/{activityId}/submit', [GameActivityController::class, 'submit']);
	Route::get('/shop', [ShopController::class, 'index']);
	Route::post('/shop/{shopItem}/purchase', [ShopController::class, 'purchase']);
	Route::post('/shop/{shopItem}/toggle-equipped', [ShopController::class, 'toggleEquipped']);
	Route::middleware('admin')->prefix('admin')->group(function () {
		Route::get('/dashboard', [AdminController::class, 'dashboard']);
		Route::post('/subjects', [AdminController::class, 'storeSubject']);
		Route::post('/activities', [AdminController::class, 'storeActivity']);
		Route::post('/daily-challenges', [AdminController::class, 'storeChallenge']);
	});
});
