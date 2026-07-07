<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        if (! $this->app->runningInConsole()) {
            $request = $this->app->make('request');

            if ($request->hasHeader('Host')) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }
        }

        if (parse_url((string) config('app.url'), PHP_URL_SCHEME) === 'https') {
            URL::forceScheme('https');
        }

        Gate::before(function ($user, $ability) {
            return $user->role_id == 1 ? true : null;
        });
    }
}
