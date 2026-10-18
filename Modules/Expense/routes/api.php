<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Expense\App\Http\Controllers\Api\ExpenseController;
use Modules\Expense\App\Http\Controllers\Api\ExpenseStudentController;
use Modules\Expense\App\Http\Controllers\Api\ExpenseStudentAdminController;

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
    Route::apiResource('expenses', ExpenseController::class)->only(['index', 'store']);
    Route::post('expenses/{expense}', [ExpenseController::class, 'update']);
    Route::get('expenses/{expense}/exceptions', [ExpenseController::class, 'exceptions']);
    Route::post('expenses/{expense}/exceptions', [ExpenseController::class, 'storeExceptions']);
    Route::post('expenses/{expense}/exceptions/update', [ExpenseController::class, 'updateExceptions']);
    Route::post('expenses/{expense}/exceptions/delete', [ExpenseController::class, 'deleteExceptions']);
    Route::get('expenses/students', [ExpenseController::class, 'students']);

    Route::apiResource('student-expenses', ExpenseStudentAdminController::class)->only(['index','store']);
    Route::post('student-expenses/{studentExpense}', [ExpenseStudentAdminController::class, 'update']);
});

Route::group(['prefix' => 'student'], function () {
    Route::get('required-expenses', [ExpenseStudentController::class, 'requiredExpenses']);
    Route::apiResource('expenses', ExpenseStudentController::class)->only(['index', 'store']);
    Route::post('expenses/{studentExpense}', [ExpenseStudentController::class, 'update']);
});

Route::get('expenses', [ExpenseStudentController::class, 'expenses']);
