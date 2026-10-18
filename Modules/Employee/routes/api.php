<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\App\Http\Controllers\Api\EmployeeAdminController;

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
Route::group(['prefix' => "admin"], function () {
    Route::post("employees/import", [EmployeeAdminController::class, 'import']);
    Route::apiResource("employees", EmployeeAdminController::class)->only(['index','store']);
  Route::post("employees/{user}", [EmployeeAdminController::class, 'update']);
    //Roles
    Route::get("employee-roles", [EmployeeAdminController::class, "employeeRoles"]);
});


// Route::group([
//     'prefix' => 'employee'
// ], function ($router) {
//     Route::group(['prefix' => 'auth'], function ($router) {
//         Route::post('login', [EmployeeAuthController::class, 'login']);
//         Route::post('logout', [EmployeeAuthController::class, 'logout']);
//         Route::post('refresh', [EmployeeAuthController::class, 'refresh']);
//         Route::post('me', [EmployeeAuthController::class, 'me']);
//     });
//     // Route::post('change-password', [UserController::class, 'changePassword']);
//     // Route::post('update-profile', [UserController::class, 'updateProfile']);
// });
