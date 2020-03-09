<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function map()
    {
        $this->mapApiAuthRoutes();
    }

    private function getNamespace($complemento = null)
    {
        return $complemento ? "$this->namespace\\$complemento" : $this->namespace;
    }

    protected function mapApiAuthRoutes()
    {
        Route::prefix('api')->middleware(['api'])->namespace($this->getNamespace('Auth'))->group(base_path('routes/api/auth/auth.php'));
        Route::prefix('api')->middleware(['api'])->namespace($this->getNamespace('Auth'))->group(base_path('routes/api/auth/redefinir-senha.php'));
    }
}
