<?php

namespace JbAuthJwt\Middleware;

use Closure;
use JbAuthJwt\Exceptions\AuthException;

class VerificarPapelUsuario
{
    public function handle($request, Closure $next, $papel)
    {
        $papeis = explode('|',$papel);
        if ( ! in_array(auth()->user()->usuario->papel, $papeis)) {
            throw new AuthException("Você não tem permissão para acessar esse recurso.");

        }
        return $next($request);
    }

}
