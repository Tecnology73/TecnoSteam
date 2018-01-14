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

/*Route::get('test', function() {
	return \Auth::user()->createToken('app')->accessToken;
});*/

Route::get('/', 'HomeController@index')->name('home');

Route::group(['prefix' => 'forums', 'middleware' => 'auth'], function () {
	Route::get('/', 'ForumController@index')->name('forums.home');
	Route::post('/', 'ForumController@suggest');

	Route::get('{suggestion}/vote/{type}', 'ForumController@vote')->name('forums.vote');
});

// Auth
Route::get('/login', 'Auth\LoginController@login')->name('login');
Route::get('/login/callback', 'Auth\LoginController@callback');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
