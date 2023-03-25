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
    Route::group(['prefix' => '/histories', 'middleware' => ['jwt.auth']], function() {
        Route::get('/historiesWeek', 'HistoryController@getHistoriesWeek');
        Route::get('/time/{type}', 'HistoryController@HistoriesTime');
        Route::get('/bot-orders', 'HistoryController@getBotOrders');
        Route::post('/stastics/{type}', 'HistoryController@getStastics');
        Route::post('/{type}', 'HistoryController@getHistories');
    });
});