<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Session\App\Http\Controllers\Api\SessionController;
use Modules\Session\App\Http\Controllers\Api\AttendanceController;

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

Route::delete('sessions/delete-multiple', [SessionController::class, 'deleteMultiple']);
Route::apiResource('sessions', SessionController::class)->only(['index', 'store', 'destroy']);
Route::post('sessions/import', [SessionController::class, 'importSessions']);
Route::post('sessions/{session}', [SessionController::class, 'update']);
Route::apiResource('attendances', AttendanceController::class);