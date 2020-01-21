<?php

namespace JbAuthJwt\Auth;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class PasswordBroker extends \Illuminate\Auth\Passwords\PasswordBroker
{
    protected $tokens;

    protected $users;

    protected $senhaValidator;

    public function __construct(
        \Illuminate\Auth\Passwords\TokenRepositoryInterface $tokens,
        \Illuminate\Contracts\Auth\UserProvider $users
    ) {
        $this->users = $users;
        $this->tokens = $tokens;
    }

    public function sendResetLink(array $credentials)
    {
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        $user->sendPasswordResetNotification(
            $this->tokens->create($user)
        );

        return static::RESET_LINK_SENT;
    }

    public function reset(array $credentials, Closure $callback)
    {
        $user = $this->validateReset($credentials);

        if (! $user instanceof CanResetPasswordContract) {
            return $user;
        }

        $senha = $credentials['senha'];

        $callback($user, $senha);

        $this->tokens->delete($user);

        return static::PASSWORD_RESET;
    }

    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser(['email'=>$credentials['email']]))) {
            return static::INVALID_USER;
        }


        if (! $this->validateNewPassword($credentials)) {
            return static::INVALID_PASSWORD;
        }

        if (! $this->tokenExists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }

    public function validator(Closure $callback)
    {
        $this->senhaValidator = $callback;
    }

    public function validateNewPassword(array $credentials)
    {
        if (isset($this->senhaValidator)) {
            [$senha, $confirm] = [
                $credentials['senha'],
                $credentials['senha_confirmation'],
            ];

            return call_user_func(
                $this->senhaValidator,
                $credentials
            ) && $senha === $confirm;
        }

        return $this->validatePasswordWithDefaults($credentials);
    }

    protected function validatePasswordWithDefaults(array $credentials)
    {
        [$senha, $confirm] = [
            $credentials['senha'],
            $credentials['senha_confirmation'],
        ];

        return $senha === $confirm && mb_strlen($senha) >= env('APP_PASSWORD_MIN', 6);
    }

    public function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token']);

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanResetPasswordContract) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    public function createToken(CanResetPasswordContract $user)
    {
        return $this->tokens->create($user);
    }

    public function deleteToken(CanResetPasswordContract $user)
    {
        $this->tokens->delete($user);
    }

    public function tokenExists(CanResetPasswordContract $user, $token)
    {
        return $this->tokens->exists($user, $token);
    }

    public function getRepository()
    {
        return $this->tokens;
    }
}
