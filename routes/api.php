<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListShareController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use Illuminate\Support\Facades\Route;


/**
 * <h3>Authentication Routes<h3>
 */

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'sendPasswordResetLink']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

/**
 * <h3>Protected Routes<h3>
 */
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    /**
     * <h3>Task List Routes<h3>
     */
    Route::prefix('task-lists')->group(function () {
        Route::get('', [TaskListController::class, 'index']);
        Route::post('', [TaskListController::class, 'store']);
        Route::get('{taskList}', [TaskListController::class, 'show']);
        Route::put('{taskList}', [TaskListController::class, 'update']);
        Route::delete('{taskList}', [TaskListController::class, 'destroy']);

        /**
         * <h3>Task List Sharing Routes<h3>
         */
        Route::get('check-username/{username}', [TaskListController::class, 'checkUsername']);
        Route::post('{taskList}/share', [ListShareController::class, 'share']);
        Route::post('{taskList}/un-share', [ListShareController::class, 'unShare']);
        Route::put('{taskList}/update-permission', [ListShareController::class, 'update']);
        Route::get('{taskList}/shared-with', [ListShareController::class, 'shared']);

        /**
         * <h3>Tasks Routes<h3>
         */
        Route::prefix('{taskList}/tasks')->group(function () {
            Route::get('', [TaskController::class, 'index']);
            Route::post('', [TaskController::class, 'store']);
            Route::get('{task}', [TaskController::class, 'show']);
            Route::put('{task}', [TaskController::class, 'update']);
            Route::delete('{task}', [TaskController::class, 'destroy']);
        });
    });
});
