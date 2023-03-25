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

Route::group(['middleware' => ['jwt.auth', 'maintenance']], function() {
    Route::group(['prefix' => '/trading'], function() {
        Route::post('/changePlayMode', 'TradingController@changePlayMode');
        Route::post('/placed', 'TradingController@Placed');
        Route::post('/addShapeId', 'TradingController@addShapeId');
        Route::get('/stastics', 'TradingController@stastics');
        Route::get('/getPendingOrder', 'TradingController@getPendingOrder');
        Route::get('/getResult', 'TradingController@getResult');
    });
});