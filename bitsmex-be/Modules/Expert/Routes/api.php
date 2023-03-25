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
    Route::group(['prefix' => '/expert', 'middleware' => ['jwt.auth']], function() {
        Route::get('/information', 'ExpertController@getInformation');
        Route::get('/list', 'ExpertController@getExperts');
        Route::get('/{expertid}', 'ExpertController@getExpert');
        Route::post('/stop', 'ExpertController@stopCopy');
        Route::post('/registration', 'ExpertController@postRegistration');
        Route::post('/copying', 'ExpertController@copying');
        Route::post('/addfunds', 'ExpertController@addfunds');
    });
});