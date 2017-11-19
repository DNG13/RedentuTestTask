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
Route::get('/upload','ImageController@index')->name('upload');
Route::post('/upload','ImageController@upload');
Route::get('/show','ImageController@show')->name('show');

Route::get('/upload1','ImageController@index1')->name('upload1');
Route::post('/upload1','ImageController@upload1');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
