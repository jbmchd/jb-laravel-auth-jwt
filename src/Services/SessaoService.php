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

        $auth = [
            'pessoa' => $pessoa,
            'usuario' => $usuario,
        ];

        if(isset($dados['auth'])){
            $auth = array_merge($auth, $dados['auth']);
            unset($dados['auth']);
        }

        $sessao_inicial = array_merge(['auth'=>$auth], $dados);

        //iniciar sessão
        self::session($sessao_inicial);

        return $sessao_inicial;
    }

}
