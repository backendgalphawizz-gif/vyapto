<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
// use Spatie\Permission\Middlewares\RoleMiddleware;
// use Spatie\Permission\Middlewares\PermissionMiddleware;
// use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // //Register Spatie middleware aliases
        // Route::aliasMiddleware('role', RoleMiddleware::class);
        // Route::aliasMiddleware('permission', PermissionMiddleware::class);
        // Route::aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
    }
}
