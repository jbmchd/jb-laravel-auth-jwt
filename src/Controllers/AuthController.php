<?php

namespace JbAuthJwt\Controllers;

use JbAuthJwt\Services\Auth\AuthService;
use JbAuthJwt\Services\Auth\ResetarSenhaEmailService;
use JbAuthJwt\Services\Auth\ResetarSenhaService;
use Illuminate\Http\Request;

use JbGlobal\Controllers\Controller;

class AuthController extends Controller
{
    protected $servico;
    protected $resetar_senha_email_servico;
    protected $resetar_senha_servico;

    public function __construct(AuthService $servico, ResetarSenhaEmailService $resetar_senha_email_servico, ResetarSenhaService $resetar_senha_servico)
    {
        $this->servico = $servico;
        $this->resetar_senha_email_servico = $resetar_senha_email_servico;
        $this->resetar_senha_servico = $resetar_senha_servico;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'senha');
        $dados = $this->servico->login($credentials);
        $retorno = self::criarRetornoController($dados);
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
        $retorno = self::criarRetornoController($dados);
        return response()->json($retorno);
    }

    public function refresh()
    {
        $this->servico->atualizarJwtToken();
        $retorno = self::criarRetornoController($this->servico->jwtTokenInfo(), 'Token atualizado com sucesso.');
        return response()->json($retorno);
    }

    public function resetarSenhaEmail(Request $request)
    {
        $email = request('email');
        $result = $this->resetar_senha_email_servico->resetarSenhaEmail($email);
        $retorno = self::criarRetornoController($result);
        return response()->json($retorno, $retorno['code']);
    }

    public function resetarSenha(Request $request)
    {
        $result = $this->resetar_senha_servico->resetarSenha($request);
        $retorno = self::criarRetornoController($result);
        return response()->json($retorno, $retorno['code']);
    }

    public function validarTokenSenha(Request $request)
    {
        $credentials = $request->only('email', 'token');
        $result = $this->resetar_senha_email_servico->validarTokenSenha($credentials);
        $retorno = self::criarRetornoController($result);
        return response()->json($retorno, $retorno['code']);
    }
}
