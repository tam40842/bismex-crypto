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
    Route::group(['prefix' => '/wallet'], function() {
        Route::get('/getCurrencies', 'WalletController@getCurrencies');
        Route::group(['prefix' => '/overview'], function() {
            Route::post('/transactions', 'WalletController@postTransactionsAccount');
            Route::post('/postOverview', 'WalletController@postOverview');
        });

        Route::group(['prefix' => '/deposit'], function() {
            Route::post('/getHistories', 'WalletController@postDepositHistories');
            // Route::post('/searchDeposit', 'WalletController@postSearchDeposit');
            Route::get('/getDemoBalance', 'WalletController@getDemoBalance');
        });
        Route::group(['prefix' => '/transfer'], function() {
            Route::post('/postTransfer', 'WalletController@postTransfer');
            Route::post('/getHistories', 'WalletController@postTransferHistories');
            // Route::post('/searchTransfer', 'WalletController@postSearchTransfer');
        });
        Route::group(['prefix' => '/withdraw'], function() {
            Route::post('/postWithdraw', 'WalletController@postWithdraw');
            Route::post('/getHistories', 'WalletController@postWithdrawHistories');
            // Route::post('/searchWithdraw', 'WalletController@postSearchWithdraw');
        });
        Route::group(['prefix' => '/wallettransfer'], function() {
            Route::post('/', 'WalletController@WalletTransfer');
            Route::get('/getHistories', 'WalletController@getwallettransferHistories');
        });
        Route::group(['prefix' => '/exchange'], function() {
            Route::post('/', 'WalletController@exchange');
            Route::get('/getHistories', 'WalletController@ExchangeHistories');
            Route::post('/searchConvert', 'WalletController@postSearchConvert');
        });
    });
});