<?php

namespace JbAuthJwt\Services\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Support\Facades\Password;
use JbAuthJwt\Exceptions\RedefinirSenhaException;
use JbGlobal\Repositories\Pessoas\PessoaRepository as Repository;
use JbGlobal\Services\Service;

class RedefinirSenhaEmailService extends Service
{
    use SendsPasswordResetEmails;

    protected $pessoa_repo;

    public function __construct(Repository $pessoa_repo)
    {
        $this->pessoa_repo = $pessoa_repo;
    }

    public function redefinirSenhaEmail($email)
    {
        $pessoa = $this->pessoa_repo->encontrarPor('email',$email);
        if ($pessoa) {
            $result = $this->sendResetLinkEmail($pessoa->email);
            if ($result['erro']) {
                throw new RedefinirSenhaException($result['mensagem']);
            }
            return [[],$result['mensagem']];
        } else {
            throw new RedefinirSenhaException('Não foi encontrado usuário com este email');
        }
    }

    public function sendResetLinkEmail($email)
    {
        $response = $this->broker()->sendResetLink(['email'=>$email]);
        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse()
                    : $this->sendResetLinkFailedResponse();
    }

    public function sendResetLinkResponse()
    {
        return ['erro'=>false, 'mensagem'=>'Email para redefinir a senha enviado com sucesso.'];
    }

    public function sendResetLinkFailedResponse()
    {
        return ['erro'=>true, 'mensagem'=>'Falha ao enviar o email para redefinir senha.'];
    }

    public function tokenValido(array $credentials)
    {
        $pessoa = $this->getUser($credentials);
        if (!$pessoa) {
            throw new RedefinirSenhaException("Usuário não encontrado.");
        }
        $token_valido = $this->broker()->tokenExists($pessoa, $credentials['token']);
        if (! $token_valido) {
            throw new RedefinirSenhaException("Token inválido ou expirado");
        }
        return true;
    }

    public function getUser(array $credentials)
    {
        return $this->broker()->getUser($credentials);
    }
}
