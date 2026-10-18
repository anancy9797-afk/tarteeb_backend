<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\App\Http\Controllers\Api\NotificationController;

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
    'prefix' => 'notification',
], function ($router) {

    Route::get('all', [NotificationController::class, 'index']);
    Route::post('read', [NotificationController::class, 'readNotification']);
    Route::get('unReadNotificationsCount', [NotificationController::class, 'unReadNotificationsCount']);
});
