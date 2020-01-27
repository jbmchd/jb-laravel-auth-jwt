<?php

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/

Route::post('redefinir-senha/email', 'AuthRedefinirSenhaController@redefinirSenhaEmail');
Route::post('redefinir-senha', 'AuthRedefinirSenhaController@redefinirSenha');
Route::post('redefinir-senha/token-valido', 'AuthRedefinirSenhaController@tokenValido');

