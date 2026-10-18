<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\App\Http\Controllers\Api\PurchaseController;
use Modules\Purchase\App\Http\Controllers\Api\PurchaseAdminController;

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
Route::group(['prefix' => 'admin'],function(){
    Route::get('purchases', [PurchaseAdminController::class, 'index']);
    Route::post('purchases/accept', [PurchaseAdminController::class, 'accept']);
    Route::post('purchases/{purchase}/reject', [PurchaseAdminController::class, 'reject']);
});

Route::get('my-purchases', [PurchaseController::class, 'myPurchases']);
Route::post('purchases', [PurchaseController::class, 'store']);
Route::post('purchases/{purchase}', [PurchaseController::class, 'update']);
