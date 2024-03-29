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

Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\RegisterController@login');
   
Route::middleware('auth:api')->group( function () {
    Route::post('update-profile', 'API\ProfileController@update');
    Route::post('deposit', 'API\TransactionController@store');
    Route::post('transfer', 'API\TransactionController@transfer');
    Route::post('get-balance', 'API\AccountController@getBalance');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
