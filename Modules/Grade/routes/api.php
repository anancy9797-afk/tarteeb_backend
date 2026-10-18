<?php

use Illuminate\Support\Facades\Route;
use Modules\Grade\App\Http\Controllers\Api\GradeController;
use Modules\Grade\App\Http\Controllers\Api\GradeCategoryController;

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

Route::apiResource('grade-categories', GradeCategoryController::class);
Route::apiResource('grades', GradeController::class);
Route::get('grade-categories/{grade_category}/grades', [GradeController::class, 'getGradesByGradeCategory']);
