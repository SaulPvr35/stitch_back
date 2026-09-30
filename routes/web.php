<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'Aventura del Saber API',
        'status' => 'ok',
    ]);
});
