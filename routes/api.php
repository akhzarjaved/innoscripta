<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
Route::post('otp-verification', [AuthController::class, 'otpVerification']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('articles', [ArticleController::class, 'search']);
    Route::get('preferred-articles', [ArticleController::class, 'preferred']);

    Route::group(['prefix' => 'user', 'controller' => UserController::class], function () {
        Route::get('/', 'me');
        Route::get('preferences', 'preferences');
        Route::post('preferences', 'savePreferences');
    });
});
