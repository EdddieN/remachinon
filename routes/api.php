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

// Prefix is related to the URL "prefix" used to trigger the route. eg /api/v1/auth/login
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup')->middleware('ipcheck');
    Route::get('logout', 'AuthController@logout')->middleware('auth:api');
});

// You must use ['scopes:'] (empty value) to allow only tokens with an empty scope.
Route::group(['middleware' => ['auth:api', 'scopes:connect-tunnel']], function() {
    Route::get('tunnels/{uuid}/status', 'DeviceTunnelController@status')->name('api.tunnels.status');
    Route::put('tunnels/{uuid}/confirm', 'DeviceTunnelController@confirm');
});

// this should go in console.php
Route::get('tunnels/cron', 'DeviceTunnelController@cron');
