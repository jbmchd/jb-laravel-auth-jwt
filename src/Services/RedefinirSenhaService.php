<?php

namespace JbAuthJwt\Services\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

use JbAuthJwt\Exceptions\RedefinirSenhaException;
use JbGlobal\Repositories\UsuarioRepository;
use JbGlobal\Services\Service;
use JbGlobal\Services\UsuarioService;

class RedefinirSenhaService extends Service
{
    use ResetsPasswords;

    protected $usuario_repo;
    protected $usuario_servico;
    protected $redefinir_senha_email_servico;

    public function __construct(RedefinirSenhaEmailService $redefinir_senha_email_servico, UsuarioService $usuario_servico, UsuarioRepository $usuario_repo)
    {
        $this->redefinir_senha_email_servico = $redefinir_senha_email_servico;
        $this->usuario_servico = $usuario_servico;
        $this->usuario_repo = $usuario_repo;
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
            throw new RedefinirSenhaException("Não foi encontrado usuário com este CPF");
        }
    }

    public function reset(array $credentials)
    {
        $this->validar($credentials, $this->rules());

        $response = $this->broker()->reset(
            $credentials,
            function ($user, $senha) {
                $this->resetPassword($user, $senha);
            }
        );

        return $response === Password::PASSWORD_RESET
                    ? $this->sendResetResponse()
                    : $this->sendResetFailedResponse();
    }

    public function resetPassword($user, $senha)
    {
        $usuario_id = $user->usuario->id;
        $senha_hash = $this->usuario_servico->criarSenha($senha);
        $this->usuario_repo->alterarSenha($usuario_id, $senha_hash);
        event(new PasswordReset($user));
    }

    public function sendResetResponse()
    {
        return ['erro'=>false, 'mensagem'=>'Senha redefinida com sucesso.'];
    }

    public function sendResetFailedResponse()
    {
        return ['erro'=>true, 'mensagem'=>'Falha ao redefinir senha.'];
    }

    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'senha' => 'required|confirmed|min:6',
        ];
    }
}
