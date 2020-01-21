<?php

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/

Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::post('refresh', 'AuthController@refresh');

Route::post('resetar-senha-email', 'AuthController@resetarSenhaEmail');
Route::post('resetar-senha', 'AuthController@resetarSenha');
Route::post('validar-token-senha', 'AuthController@validarTokenSenha');

Route::post('me', 'AuthController@me');
