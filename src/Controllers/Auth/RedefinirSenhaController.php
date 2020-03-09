<?php

namespace JbAuthJwt\Controllers\Auth;

use JbAuthJwt\Services\Auth\RedefinirSenhaEmailService;
use JbAuthJwt\Services\Auth\RedefinirSenhaService;
use Illuminate\Http\Request;

use JbGlobal\Controllers\Controller;

class AuthRedefinirSenhaController extends Controller
{
    protected $redefinir_senha_email_servico;
    protected $redefinir_senha_servico;

    public function __construct(RedefinirSenhaEmailService $redefinir_senha_email_servico, RedefinirSenhaService $redefinir_senha_servico)
    {
        $this->redefinir_senha_email_servico = $redefinir_senha_email_servico;
        $this->redefinir_senha_servico = $redefinir_senha_servico;
    }

    public function redefinirSenhaEmail(Request $request)
    {
        $email = request('email');
        $result = $this->redefinir_senha_email_servico->redefinirSenhaEmail($email);
        $retorno = self::criarRetornoController(...$result);
        return response()->json($retorno);
    }

    public function redefinirSenha(Request $request)
    {
        $auth_user = auth()->user();
        if($auth_user){
            $credentials = $request->only('email', 'senha_atual', 'senha', 'senha_confirmation');
            if($auth_user->email == $credentials['email']){
                $result =  $this->redefinir_senha_servico->redefinirSenhaAuth($credentials);
            }
        }
        else {
            $credentials = $request->only('email', 'senha', 'senha_confirmation', 'token');
            $result =  $this->redefinir_senha_servico->redefinirSenha($credentials);
        }
        $retorno = self::criarRetornoController(...$result);

        return response()->json($retorno);
    }

    public function tokenValido(Request $request)
    {
        $credentials = $request->only('email', 'token');
        $result = $this->redefinir_senha_email_servico->tokenValido($credentials);
        $retorno = self::criarRetornoController($result);
        return response()->json($retorno);
    }
}
