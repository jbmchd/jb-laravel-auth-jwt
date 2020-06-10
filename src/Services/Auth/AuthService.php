<?php

namespace JbAuthJwt\Services\Auth;

use JbAuthJwt\Exceptions\AuthException;
use JbGlobal\Repositories\Repository;
use JbGlobal\Services\Service;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService extends Service
{
    protected $pessoa_repositorio;
    protected $sessao_servico;

    public function __construct(Repository $pessoa_repositorio, Service $sessao_servico)
    {
        $this->pessoa_repositorio = $pessoa_repositorio;
        $this->sessao_servico = $sessao_servico;
    }

    public function login($credentials)
    {
        $credentials['password'] = $credentials['senha'];
        unset($credentials['senha']);
        $token = JWTAuth::attempt($credentials);
        if ($token) {
            $me = $this->me(false);
            if($this->sessao_servico){
                $this->sessao_servico->iniciarSessaoAuth();
            }
            $me = $me->toArray();
            $dados = array_merge(['token'=> $token], ['me'=>$me, 'auth'=>self::session('auth')]);
        } else {
            throw new AuthException("Credenciais inválidas");
        }
        $dados = array_merge(['token'=> $token], ['me'=>$me, 'auth'=>self::session('auth')]);
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
        self::session([]);
        auth()->logout();
        return 'Logout feito com sucesso.';
    }

    public function auth()
    {
        return self::session('auth');
    }

    public function emailExiste($email, $ignorar_id=0)
    {
        return $this->validar(['email'=>$email], [
            "email" => "required|string|email|max:255|unique:pessoas,email,$ignorar_id,id",
        ], false);
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
