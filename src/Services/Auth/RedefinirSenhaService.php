<?php

namespace JbAuthJwt\Services\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

use JbAuthJwt\Exceptions\RedefinirSenhaException;
use JbGlobal\Repositories\Pessoas\UsuarioRepository;
use JbGlobal\Services\Pessoas\UsuarioService;
use JbGlobal\Services\Service;

class RedefinirSenhaService extends Service
{
    use ResetsPasswords;

    protected $usuario_repo;
    protected $usuario_servico;
    protected $redefinir_senha_email_servico;

    // public function __construct(RedefinirSenhaEmailService $redefinir_senha_email_servico, UsuarioService $usuario_servico, UsuarioRepository $usuario_repo)
    public function __construct(RedefinirSenhaEmailService $redefinir_senha_email_servico, UsuarioService $usuario_servico, UsuarioRepository $usuario_repo)
    {
        // $this->redefinir_senha_email_servico = $redefinir_senha_email_servico;
        // $this->usuario_servico = $usuario_servico;
        // $this->usuario_repo = $usuario_repo;
    }

    public function redefinirSenhaAuth(array $credentials)
    {
        $Pessoa = auth()->user();
        if ($Pessoa) {
            $credentials['email'] = $Pessoa->email;

            $this->validarRedefinicaoSenha($credentials, false);

            $result = $this->resetPassword($Pessoa, $credentials['senha']);

            if ($result['erro']) {
                throw new RedefinirSenhaException($result['mensagem']);
            }

            $retorno = [!$result['erro'],$result['mensagem']];
            return $retorno;
        } else {
            throw new RedefinirSenhaException("Não foi encontrado usuário com este CPF");
        }
    }

    public function redefinirSenha(array $credentials)
    {
        $Pessoa = $this->broker()->getUser(['email'=>$credentials['email']]);
        if ($Pessoa) {
            $token_valido = $this->broker()->tokenExists($Pessoa, $credentials['token']);
            if ($token_valido) {
                $credentials['email'] = $Pessoa->email;

                $result = $this->reset($credentials);
                if ($result['erro']) {
                    throw new RedefinirSenhaException($result['mensagem']);
                }

                $retorno = [[],$result['mensagem']];
                return $retorno;
            } else {
                throw new RedefinirSenhaException("O token fornecido não é válido.");
            }
        } else {
            throw new RedefinirSenhaException("Não foi encontrado usuário com este email");
        }
    }

    public function reset(array $credentials)
    {
        $this->validarRedefinicaoSenha($credentials, true);

        $password_const = $this->broker()->reset(
            $credentials,
            function ($user, $senha) {
                return $this->resetPassword($user, $senha);
            }
        );

        return $this->getResponse($password_const);
    }

    public function resetPassword($user, $senha)
    {
        $usuario_id = $user->usuario->id;
        $senha_hash = $this->usuario_servico->criarSenha($senha);
        $this->usuario_repo->alterarSenha($usuario_id, $senha_hash);

        $result = new PasswordReset($user);
        $password_const = $result instanceof \Illuminate\Auth\Events\PasswordReset ? Password::PASSWORD_RESET : false;

        return $this->getResponse($password_const);
    }

    public function sendResetResponse()
    {
        return ['erro'=>false, 'mensagem'=>'Senha redefinida com sucesso.'];
    }

    public function sendResetFailedResponse()
    {
        return ['erro'=>true, 'mensagem'=>'Falha ao redefinir senha.'];
    }

    public function getResponse($password_const){
        return $password_const === Password::PASSWORD_RESET
                    ? $this->sendResetResponse()
                    : $this->sendResetFailedResponse();
    }

    public function validarRedefinicaoSenha($credentials, $token_obrigatorio){
        $this->validar($credentials, $this->rules($token_obrigatorio));

        if(!$token_obrigatorio){
            if (!(\Illuminate\Support\Facades\Hash::check($credentials['senha_atual'], auth()->user()->usuario->senha))) {
                // Verifica senha atual
                throw new RedefinirSenhaException('A senha atual está incorreta');
            }
            if(strcmp($credentials['senha_atual'], $credentials['senha']) == 0){
                //Senha atual e nova são iguais
                throw new RedefinirSenhaException('Sua nova senha e senha atual são as mesmas, nada foi alterado.');
            }
        }

        return true;
    }

    public function rules($token_obrigatorio=true)
    {
        $regras = [
            'email' => 'required|email',
            'senha' => 'required|confirmed|min:6',
        ];

        if($token_obrigatorio){
            $regras = array_merge($regras, ['token' => 'required']);
        }

        return $regras;
    }
}
