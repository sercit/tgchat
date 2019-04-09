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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('events','EventController@index')->name('events.index');
Route::post('events','EventController@addEvent')->name('events.add');
Route::get('services','ServiceController@index')->name('services.index');
Route::post('services','ServiceController@addService')->name('services.add');
Route::get('profile','ProfileController@show')->name('profile.show');
Route::post('profile','ProfileController@editProfile')->name('profile.edit');
//Route::post('services','ServiceController@addService')->name('services.add');
