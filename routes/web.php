<?php

use \App\Http\Controllers\Controller;

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


Route::get('/', Controller::class . '@home')->name('home');
Route::get('/team', Controller::class . '@team')->name('team');
Route::get('/progress', Controller::class . '@progress')->name('progress');
