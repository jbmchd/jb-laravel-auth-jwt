<?php

namespace JbAuthJwt\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class MailResetPasswordNotification extends ResetPassword
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        parent::__construct($token);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $url_parts = [env('APP_FRONTEND_URL', ''), 'auth', 'resetar-senha', $this->token];
        $url = implode('/', $url_parts);
        return (new MailMessage)
                    ->subject('[AÇÃO NECESSÁRIA] - Redefinição de Senha Solicitada')
                    ->line('Você recebeu este email porque houve uma solicitação para resetar a sua senha.')
                    ->action('Redefinir senha', $url)
                    ->line('Se você não solicitou este reset, apenas ignore este email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
