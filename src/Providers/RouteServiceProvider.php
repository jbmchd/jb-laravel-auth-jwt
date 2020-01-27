<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function map()
    {
        $this->mapApiAuthRoutes();
        $this->mapApiRedefinirSenhaRoutes();
    }

    private function getNamespace($complemento = null)
    {
        return $complemento ? "$this->namespace\\$complemento" : $this->namespace;
    }

    protected function mapApiAuthRoutes()
    {
        Route::prefix('api')->middleware(['api'])->namespace($this->getNamespace())->group(base_path('routes/api/auth.php'));
    }

    protected function mapApiRedefinirSenhaRoutes()
    {
        Route::prefix('api')->middleware(['api'])->namespace($this->getNamespace())->group(base_path('routes/api/redefinir-senha.php'));
    }
}
