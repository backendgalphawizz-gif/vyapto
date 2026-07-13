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

            // Prevent login redirect loops when production .env still has
            // SESSION_DOMAIN=localhost (or any host that doesn't match this request).
            $sessionDomain = config('session.domain');
            if (is_string($sessionDomain)) {
                $sessionDomain = trim($sessionDomain);
            }
            if ($sessionDomain === '' || strcasecmp((string) $sessionDomain, 'null') === 0) {
                config(['session.domain' => null]);
            } elseif (is_string($sessionDomain) && $sessionDomain !== '') {
                $host = $request->getHost();
                $normalized = ltrim($sessionDomain, '.');
                if ($host !== $normalized && ! str_ends_with($host, '.'.$normalized)) {
                    config(['session.domain' => null]);
                }
            }

            // Match Secure cookie flag to the real request scheme (behind proxies).
            if ($request->isSecure()) {
                config(['session.secure' => true]);
            }
        }

        if (parse_url((string) config('app.url'), PHP_URL_SCHEME) === 'https') {
            URL::forceScheme('https');
        }

        Gate::before(function ($user, $ability) {
            // Super admin bypass for all permissions
            if ((int) ($user->role_id ?? 0) === 1) {
                return true;
            }

            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }

            return null;
        });
    }
}
