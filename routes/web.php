<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');

Auth::routes();

Route::resource('devices', 'DeviceController');

Route::get('devices/{id}/connect', ['as' => 'devices.connect', 'uses' => 'DeviceTunnelController@connect'])->middleware('auth');
Route::get('devices/{id}/disconnect', ['as' => 'devices.disconnect', 'uses' => 'DeviceTunnelController@disconnect'])->middleware('auth');

Route::get('tunnels/{id}/status', ['as' => 'tunnels.status', 'uses' => 'DeviceTunnelController@status'])->middleware('auth');
