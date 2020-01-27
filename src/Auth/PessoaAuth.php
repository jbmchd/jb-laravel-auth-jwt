<?php
namespace JbAuthJwt\Auth;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JbAuthJwt\Notifications\MailResetPasswordNotification;
use JbGlobal\Models\PasswordReset;
use Tymon\JWTAuth\Contracts\JWTSubject;

class PessoaAuth extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthPassword()
    {
        return $this->usuario->senha;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }

    public function password_reset()
    {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

}
