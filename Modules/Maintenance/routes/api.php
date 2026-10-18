<?php

use Illuminate\Support\Facades\Route;
use Modules\Maintenance\App\Http\Controllers\Api\MaintenanceController;
use Modules\Maintenance\App\Http\Controllers\Api\MaintenanceAdminController;

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

Route::group(['prefix' => 'admin'], function () {
    Route::get('maintenances', [MaintenanceAdminController::class, 'index']);
    Route::post('maintenances/accept', [MaintenanceAdminController::class, 'accept']);
    Route::post('maintenances/{maintenance}/reject', [MaintenanceAdminController::class, 'reject']);
});

Route::get('my-maintenances', [MaintenanceController::class, 'myMaintenances']);
Route::post('maintenances', [MaintenanceController::class, 'store']);
Route::post('maintenances/{maintenance}', [MaintenanceController::class, 'update']);
