<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\Trading\Http\Controllers\TradingController;

Route::group(['middleware' => ['maintenance', 'api-token']], function () {
    Route::group(['prefix' => '/auth'], function () {
        Route::post('/user', [TelegramBotController::class, 'user']);
    });

    Route::group(['prefix' => '/orders', 'middleware' => ['maintenance']], function () {
        Route::post('/placed', [TradingController::class, 'placed']);
        Route::post('/status', [TelegramBotController::class, 'getOrderStatus']);
    }); 

    Route::group(['prefix' => '/general', 'middleware' => ['maintenance']], function () {
        Route::get('/', [TelegramBotController::class, 'getGeneral']);
    });
});

Route::group(['prefix' => '/market'], function () {
    Route::get('/blurs/{market_name}', [TelegramBotController::class, 'getBlurs']);
    Route::get('/blurs-all/{market_name}', [TelegramBotController::class, 'getBlursAll']);
});