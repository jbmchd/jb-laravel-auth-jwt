<?php

namespace JbAuthJwt\Providers;

use Illuminate\Support\Facades\Route;
use JbGlobal\Providers\RouteServiceProvider as JbGlobalServiceProvider;

class RouteServiceProvider extends JbGlobalServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        parent::map();
        $this->mapApiAuthRoutes();
    }

    protected function mapApiAuthRoutes()
    {
        Route::prefix('api')->middleware(['api'])->namespace($this->getNamespace('Auth'))->group(base_path('routes/api/auth/auth.php'));
        Route::prefix('api')->middleware(['api'])->namespace($this->getNamespace('Auth'))->group(base_path('routes/api/auth/redefinir-senha.php'));
    }
}
