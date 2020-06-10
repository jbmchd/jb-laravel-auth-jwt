<?php

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/

Route::get('jwt-token-info', 'AuthController@jwtTokenInfo');

Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::post('refresh', 'AuthController@refresh');
Route::post('me', 'AuthController@me');
Route::post('auth', 'AuthController@auth');
Route::post('email-existe', 'AuthController@emailExiste');
