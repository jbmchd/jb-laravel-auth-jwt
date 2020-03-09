<?php

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/

Route::post('redefinir-senha/email', 'RedefinirSenhaController@redefinirSenhaEmail');
Route::post('redefinir-senha', 'RedefinirSenhaController@redefinirSenha');
Route::post('redefinir-senha/token-valido', 'RedefinirSenhaController@tokenValido');

