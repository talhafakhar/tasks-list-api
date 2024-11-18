<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

use App\Http\Controllers\AuthController;
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
        Route::get('', 'App\Http\Controllers\TaskListController@index');
        Route::post('', 'App\Http\Controllers\TaskListController@store');
        Route::get('{taskList}', 'App\Http\Controllers\TaskListController@show');
        Route::put('{taskList}', 'App\Http\Controllers\TaskListController@update');
        Route::delete('{taskList}', 'App\Http\Controllers\TaskListController@destroy');
        Route::post('{taskList}/share', 'App\Http\Controllers\ListShareController@store');
        Route::get('{taskList}/shared', 'App\Http\Controllers\ListShareController@index');

        /**
         * <h3>Tasks Routes<h3>
         */
        Route::prefix('{taskList}/tasks')->group(function () {
            Route::get('', 'App\Http\Controllers\TaskController@index');
            Route::post('', 'App\Http\Controllers\TaskController@store');
            Route::get('{task}', 'App\Http\Controllers\TaskController@show');
            Route::put('{task}', 'App\Http\Controllers\TaskController@update');
            Route::delete('{task}', 'App\Http\Controllers\TaskController@destroy');
        });
    });
});
