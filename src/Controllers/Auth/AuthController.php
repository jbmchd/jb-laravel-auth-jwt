<?php

namespace JbAuthJwt\Controllers\Auth;

use JbAuthJwt\Services\Auth\AuthService;
use Illuminate\Http\Request;

use JbGlobal\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct(AuthService $servico)
    {
        $this->middleware('auth:api', ['except' => ['login','emailExiste']]);
        parent::__construct($servico);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'senha');
        $dados = $this->servico->login($credentials);
        $retorno = self::criarRetornoController($dados, 'Tudo certo vamos entrar');
        return $retorno;
    }

    public function me()
    {
        $dados = $this->servico->me();
        $retorno = self::criarRetornoController($dados);
        return $retorno;
    }

    public function logout()
    {
        $dados = $this->servico->logout();
        $retorno = self::criarRetornoController($dados, 'Você saiu do sistema');
        return $retorno;
    }

    public function refresh()
    {
        $novo_token = $this->servico->atualizarJwtToken();
        $retorno = self::criarRetornoController($novo_token, 'Token atualizado com sucesso.');
        return $retorno;
    }

    public function emailExiste(Request $request)
    {
        $email = $request->get('email');
        $ignore_id = $request->get('ignore_id') ?? 0;

        $result = $this->servico->emailExiste($email, $ignore_id);
        $retorno = self::criarRetornoController($result);
        return $retorno;
    }
}
