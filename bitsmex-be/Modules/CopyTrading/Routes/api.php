<?php

use Illuminate\Http\Request;
use Modules\CopyTrading\Http\Controllers\CopytradingController;
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
    Route::group(['prefix' => 'copytrading' , 'middleware' => ['jwt.auth']], function() {
        Route::get('/users-pending', 'CopyTradingController@getUsersPending');
        Route::get('/users-follow', 'CopyTradingController@getUserFollow');
        Route::post('/set-user', 'CopyTradingController@postSetUser');
        Route::post('/remove-user/{user_follow}', 'CopyTradingController@postRemoveUser');
        Route::get('/profileTrader', 'CopyTradingController@ProfileTrader');
        Route::post('/registration', 'CopyTradingController@postRegistration');
        Route::get('/list', 'CopyTradingController@getList');
        Route::post('/copying', 'CopyTradingController@postCopying');
        Route::get('/list-follow', 'CopyTradingController@getListFollow');
        Route::post('/detail-expert-follow', 'CopyTradingController@getExpert');
        Route::post('/wallet/{type}', 'CopyTradingController@postWallet');
        Route::post('/stop', 'CopyTradingController@stopCopy');
    });
});