<?php

namespace JbAuthJwt\Providers;

use JbAuthJwt\Auth\PasswordBrokerManager;

class PasswordResetServiceProvider extends \Illuminate\Auth\Passwords\PasswordResetServiceProvider
{
    protected function registerPasswordBroker()
    {
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });
    }
}
