<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('guest')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('deals', DealController::class);
});
