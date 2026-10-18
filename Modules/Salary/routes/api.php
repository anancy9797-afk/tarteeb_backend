<?php

use Illuminate\Support\Facades\Route;
use Modules\Salary\App\Http\Controllers\Api\SalaryController;
use Modules\Salary\App\Http\Controllers\Api\SalaryAdminController;

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
    Route::get('salaries', [SalaryAdminController::class, 'index']);

});

Route::get('my-salaries', [SalaryController::class, 'mySalaries']);
Route::get('salaries', [SalaryController::class, 'index']);
Route::post('salaries', [SalaryController::class, 'store']);
Route::post('salaries/import', [SalaryController::class, 'import']);
Route::post('salaries/{salary}', [SalaryController::class, 'update']);
