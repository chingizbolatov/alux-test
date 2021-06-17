<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'App\Http\Controllers\api\AuthController@login')->name('login');
    Route::post('register', 'App\Http\Controllers\api\AuthController@registration')->name('register');
});

Route::group(['prefix' => 'currency', 'middleware' => 'is_auth'], function () {
    Route::get('/', 'App\Http\Controllers\api\CurrenciesController@index');
    Route::get('/get_rate', 'App\Http\Controllers\api\CurrenciesController@getCurrencyPairRate');
    Route::get('/get_rate_history', 'App\Http\Controllers\api\CurrenciesController@getCurrencyPairRateHistory');
});
