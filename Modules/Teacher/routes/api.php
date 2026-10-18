<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Teacher\App\Http\Controllers\Api\TeacherController;

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


Route::apiResource('teachers',TeacherController::class);
Route::get('subjects/{subject}/teachers', [TeacherController::class, 'getTeachersBySubjectId']);
Route::post('teachers/import', [TeacherController::class, 'importTeachers']);