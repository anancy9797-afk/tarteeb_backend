<?php

use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\Api\UserController;
use Modules\User\App\Http\Controllers\Api\UserAuthController;
use Modules\User\App\Http\Controllers\Api\DashboardController;
use Modules\User\App\Http\Controllers\Api\PasswordResetController;
use Modules\User\App\Http\Controllers\Api\UserAdminController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::group([
    'prefix' => 'user'
], function ($router) {
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::post('login', [UserAuthController::class, 'login']);
        Route::post('logout', [UserAuthController::class, 'logout']);
        Route::post('refresh', [UserAuthController::class, 'refresh']);
        Route::post('me', [UserAuthController::class, 'me']);
    });
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);

    // Password Reset API Routes for React
    Route::post('forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('validate-reset-token', [PasswordResetController::class, 'validateResetToken']);
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword']);
});

//Dashboard
Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('attendance-statistics', [DashboardController::class, 'getAttendanceStatistics']);
Route::get('todays-attendance-statistics', [DashboardController::class, 'getTodaysAttendanceStatistics']);
Route::get('attendance-statistics-comparison', [DashboardController::class, 'getAttendanceStatisticsComparison']);
Route::get('weekly-attendance-report', [DashboardController::class, 'getWeeklyAttendanceReport']);
Route::get('gender-statistics', [DashboardController::class, 'getGenderStatistics']);

Route::group(['prefix' => 'admin'], function () {
    Route::post('user/{user}/toggle-activate', [UserAdminController::class, 'toggleActivate']);
});
