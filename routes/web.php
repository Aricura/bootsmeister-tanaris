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
	return view('pages/home');
});

Route::get('/team', function () {
	return view('pages/team');
});

Route::get('/progress', function () {
	return view('pages/progress');
});