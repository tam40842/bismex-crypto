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
    Route::group(['prefix' => '/commission', 'middleware' => ['jwt.auth']], function() {
        Route::get('/overview', 'CommissionController@overview');
        Route::get('/overview/chart', 'CommissionController@getChart');
        Route::post('/histories', 'CommissionController@histories');
        // Route::post('/getHistoriesFilter', 'CommissionController@getHistoriesFilter');
        Route::get('/management', 'CommissionController@listChild');
        Route::post('/management/searcher', 'CommissionController@searchByUsername');
        Route::post('/management/getUserbyLevel', 'CommissionController@getUserbyLevel');
        Route::get('/getPersonalLevel', 'CommissionController@getPersonalLevel');//author ngocduoc
        Route::get('/getLevels', 'CommissionController@getLevels');//author gia
        Route::get('/tree/{email}', 'CommissionController@get_sponsorTree');
        Route::get('detail/{id}', 'CommissionController@getDetail');
        Route::post('/total-volume-f1', 'CommissionController@getTotalVolumeF1');
    });

    Route::group(['prefix' => '/franchise', 'middleware' => ['jwt.auth']], function() {
        Route::get('/overview', 'CommissionController@overviewFranchise');
        Route::get('/histories', 'CommissionController@getHistoryFranchise');
        Route::get('/historiesDays', 'CommissionController@getHistoryOverviewGroupDay');
    });
});