<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('posts', [PostController::class, 'index']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('posts/create', [PostController::class, 'create']);
    Route::get('posts/{post}/edit', [PostController::class, 'edit']);
    Route::post('posts', [PostController::class, 'store']);
    Route::put('posts/{post}', [PostController::class, 'update']);
    Route::patch('posts/{post}', [PostController::class, 'update']);
    Route::delete('posts/{post}', [PostController::class, 'destroy']);
});

Route::get('posts/{post}', [PostController::class, 'show']);

Route::post('login', [AuthController::class, 'login']);
