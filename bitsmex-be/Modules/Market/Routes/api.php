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

Route::group(['prefix' => 'markets', 'middleware' => ['maintenance']], function() {
    Route::get('/list', 'MarketController@getMarkets');
    Route::group(['middleware' => ['jwt.auth']], function() {
        Route::get('/blurs/{market_name}', 'MarketController@getBlurs');
        Route::post('/changeMarket', 'MarketController@saveMarket');
    });
});