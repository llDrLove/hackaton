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

Route::post('/auth', 'AuthController@index');

Route::post('/data/{user}', 'DataController@index');

Route::post('/location/{user}', 'LocationController@update');

Route::post('/health/{user}', 'HealthController@update');

Route::post('/health/restore/{user}', 'HealthController@restore');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
