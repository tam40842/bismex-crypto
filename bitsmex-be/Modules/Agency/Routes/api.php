<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['maintenance']], function() {
    Route::group(['prefix' => '/agency', 'middleware' => ['jwt.auth']], function() {
        Route::get('/active', 'AgencyController@getActive');
        Route::get('/tree', 'AgencyController@getTree');
    });

    Route::group(['prefix' => '/franchise', 'middleware' => ['jwt.auth']], function() {
        Route::post('/active', 'AgencyController@activeFranchise');
    });
});