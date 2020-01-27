<?php

namespace JbAuthJwt\Services\Auth;

use JbAuthJwt\Exceptions\AuthException;

use JbGlobal\Services\Service;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService extends Service
{
    public function login($credentials)
    {
        $credentials['password'] = $credentials['senha'];
        unset($credentials['senha']);
        $token = JWTAuth::attempt($credentials);
        if ($token) {
            $me = $this->me();
            $dados = array_merge(['token'=> $token], ['pessoa'=>$me]);
        } else {
            throw new AuthException("Credenciais inválidas");
        }
        $dados = array_merge(['token'=> $token], ['pessoa'=>$me]);
        return $dados;
    }

    public function me($to_array=true)
    {
        try {
            $auth = auth()->userOrFail();
            $me = $auth->with(['usuario'])->find($auth->id);
            if ($to_array) {
                $me = $me->toArray();
            }
            return $me;
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            throw new AuthException("Token não existe ou expirou");
        }
    }

    public function logout()
    {
        auth()->logout();
        return 'Logout feito com sucesso.';
    }

    public function atualizarJwtToken()
    {
        $novo_token = auth()->refresh(true, true);
        return $novo_token;
    }

    public function pegarJwtToken()
    {
        $token = JWTAuth::getToken();
        return $token->get();
    }

    public function jwtTokenInfo(string $token=null)
    {
        return [
            'token' => $token ?? self::pegarJwtToken(),
            'tipo' => 'bearer',
            'expira_em' => auth()->factory()->getTTL() * 60
        ];
    }
}
