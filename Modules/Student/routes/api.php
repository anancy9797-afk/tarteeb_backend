<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Student\App\Http\Controllers\Api\StudentController;
use Modules\Student\App\Http\Controllers\Api\StudentFeeController;
use Modules\Student\App\Http\Controllers\Api\StudentFeeAdminController;
use Modules\Student\App\Http\Controllers\Api\StudentRegisterController;
use Modules\Student\App\Http\Controllers\Api\StudentRegisterAdminController;

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

Route::apiResource('students', StudentController::class)->only(['index', 'store']);
Route::post('students/{student}/delete', [StudentController::class, 'destroy']);
Route::group(['prefix' => 'students'], function () {
    Route::get('graduate', [StudentController::class, 'getStudentsToGraduate']);
    Route::post('graduate', [StudentController::class, 'graduate']);
    Route::post('upgrade', [StudentController::class, 'upgrade']);

    //Register Student
    Route::post('register', [StudentRegisterController::class, 'register']);
    Route::get('schools', [StudentRegisterController::class, 'schools']);
    Route::post('upload-register-fee-receipt', [StudentRegisterController::class, 'uploadRegisterFeeReceipt']);
    Route::get('schools/{school_id}/grade-categories', [StudentRegisterController::class, 'gradeCategories']);
    Route::get('grade-categories/{grade_category_id}/grades', [StudentRegisterController::class, 'grades']);
});
Route::get('classes/{class}/students/upgrade', [StudentController::class, 'getStudentsToUpgrade']);
Route::post('students/import', [StudentController::class, 'importStudents']);
Route::post('students/{student}', [StudentController::class, 'update']);



//Admin Student Fees
Route::group(['prefix' => 'admin'], function () {
    Route::apiResource('student-fees', StudentFeeAdminController::class)->only(['index']);
    Route::post('student-fees/{studentFee}', [StudentFeeAdminController::class, 'update']);
    Route::get('students-to-register', [StudentRegisterAdminController::class, 'index']);
    Route::post('students-to-register/{student}/mark-as-paid', [StudentRegisterAdminController::class, 'markAsPaid']);
    Route::post('students-to-register/{student}/reject-register-fee', [StudentRegisterAdminController::class, 'rejectRegisterFee']);
    Route::post('upload-register-fee-receipt', [StudentRegisterAdminController::class, 'uploadRegisterFeeReceipt']);
});

Route::get('student-fees', [StudentFeeController::class, 'myFees']);
Route::post('student-fees', [StudentFeeController::class, 'store']);
Route::post('student-fees/{studentFee}', [StudentFeeController::class, 'update']);
