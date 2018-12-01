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

Route::group(['middleware' => 'auth'], function() {
    // Updating own User details
    Route::get('profile/edit', 'Auth\RegisterController@edit')->name('profile.edit');
    Route::patch('profile/update', 'Auth\RegisterController@update')->name('profile.update');

    Route::resource('devices', 'DeviceController');

    Route::get('devices/{id}/connect', 'DeviceTunnelController@connect')->name('devices.connect');
    Route::get('devices/{id}/disconnect', 'DeviceTunnelController@disconnect')->name('devices.disconnect');
    Route::get('tunnels/{id}/status', 'DeviceTunnelController@status')->name('tunnels.status');
});
