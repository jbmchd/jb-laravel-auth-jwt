<?php

namespace JbAuthJwt\Services;

use JbGlobal\Services\SessaoService as JbGlobalSessaoService;
use JbGlobal\Traits\TSessao;

class SessaoService extends JbGlobalSessaoService  {
    use TSessao;

    public static function iniciarSessaoAuth($dados = []){
        $pessoa = auth()->user();

        $usuario = $pessoa->usuario->toArray();
        $pessoa = $pessoa->toArray();
        unset($pessoa['usuario']);

        $sessao_inicial = array_merge([
            'auth' => [
                'pessoa' => $pessoa,
                'usuario' => $usuario,
            ]
        ], $dados);

        //iniciar sessão
        self::session($sessao_inicial);

        return $sessao_inicial;
    }

}
