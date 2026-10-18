<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Country\App\Models\State;
use Modules\Country\App\Models\Branch;
use Modules\Country\App\Models\Region;
use Modules\Country\App\Models\Governorate;

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

Route::get('governorates', function () {
    return returnMessage(true, 'Governorates fetched successfully', Governorate::all());
});

Route::get('governorates/{governorate_id}/states', function ($governorate_id) {
    return returnMessage(true, 'States fetched successfully', State::where('governorate_id', $governorate_id)->get());
});

Route::get('states/{state_id}/regions', function ($state_id) {
    return returnMessage(true, 'Regions fetched successfully', Region::where('state_id', $state_id)->get());
});

Route::get('branches', function () {
    return returnMessage(true, 'Branches fetched successfully', Branch::all());
});
