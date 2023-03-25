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
    Route::group(['prefix' => '/robot', 'middleware' => ['jwt.auth']], function() {
        Route::get('/packages', 'RobotController@getRobots');
        Route::post('/invesment', 'RobotController@Invesment');
        Route::get('/my-packages', 'RobotController@getMyPackages');
        Route::get('/histories/{orderid}', 'RobotController@histories');
    });
});