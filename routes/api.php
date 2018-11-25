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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
    });
});

//Route::get('tunnels/{uuid}/status', ['as' => 'api.tunnels.status', 'uses' => 'DeviceTunnelController@status'])
//     ->middleware(['auth:api', 'scopes:connect-tunnel']);

Route::post('tunnels/{uuid}/confirm', 'DeviceTunnelController@confirm')
     ->middleware(['auth:api', 'scopes:connect-tunnel']);

Route::get('tunnels/cron', 'DeviceTunnelController@cron');
