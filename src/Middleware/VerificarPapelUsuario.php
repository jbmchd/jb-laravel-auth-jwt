<?php

namespace JbAuthJwt\Middleware;

use Closure;
use JbAuthJwt\Exceptions\AuthException;
use JbGlobal\Traits\TSessao;

class VerificarPapelUsuario
{
    public function handle($request, Closure $next, $papel)
    {
        $tipos_papeis_permitidos = explode('|',$papel);
        $tipo_papel_usuario = TSessao::session('auth.tipo_papel');
        if ( ! in_array($tipo_papel_usuario, $tipos_papeis_permitidos)) {
            throw new AuthException("Você não tem permissão para acessar esse recurso.");

        }
        return $next($request);
    }

}
