<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('posts/create', [PostController::class, 'create']);
    Route::put('posts/edit', [PostController::class, 'edit']);
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
});
Route::apiResource('posts', PostController::class)->only(['index', 'show']);

Route::post('login', [AuthController::class, 'login']);
