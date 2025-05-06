<?php

use App\Http\Controllers\API\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(
    function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/user-data', [AuthController::class, 'me']);
            Route::apiResource('/todos', App\Http\Controllers\API\V1\TodoController::class);

            Route::get('/todo-complete/{todo}', [App\Http\Controllers\API\V1\TodoController::class, 'toggleComplete']);
        });
    });
