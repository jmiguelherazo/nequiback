<?php

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

Route::get('/nequi', 'ApiController@nequiTest');
Route::get('/goals', 'ApiController@goals');
Route::get('/me', 'ApiController@me');
Route::get('/me/goals', 'ApiController@myGoals');
Route::get('/me/pockets', 'ApiController@myPockets');
Route::post('/me/update/pocket2', 'ApiController@updatePocket2');
