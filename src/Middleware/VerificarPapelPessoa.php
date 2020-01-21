<?php

namespace JbAuthJwt\Middleware;

use Closure;
use JbAuthJwt\Exceptions\AuthException;

class VerificarPapelPessoa
{
    public function handle($request, Closure $next, $papel)
    {
        $papeis = explode('|',$papel);
        if ( ! in_array($request->user()->papel, $papeis)) {
            throw new AuthException("Você não tem permissão para acessar esse recurso.");

        }

        return $next($request);
    }

}
