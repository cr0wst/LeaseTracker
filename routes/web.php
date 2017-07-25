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

// Putting a route group here ensures the active menu will be selected for all VehicleController routes.
Route::group(['_active_menu' => 'vehicle'], function() {
    Route::get('/', 'VehicleController@index');
    Route::resource('vehicle', 'VehicleController');
});

Route::group(['_active_menu' => 'mileage'], function() {
    Route::get('mileage/{id}/create', 'MileageController@create')->name('mileage.create');
    Route::post('mileage/{id}/store', 'MileageController@store')->name('mileage.store');
});

