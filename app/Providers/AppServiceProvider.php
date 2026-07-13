<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        if (! $this->app->runningInConsole()) {
            $request = $this->app->make('request');
            $host = $request->getHost();

            if ($request->hasHeader('Host')) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }

            // Only force https when the request is actually secure (avoids http↔https refresh loops).
            if ($request->isSecure()) {
                URL::forceScheme('https');
            }

            $this->normalizeSessionConfig($host, $request->isSecure());
        } elseif (parse_url((string) config('app.url'), PHP_URL_SCHEME) === 'https') {
            URL::forceScheme('https');
        }

        Gate::before(function ($user, $ability) {
            if ((int) ($user->role_id ?? 0) === 1) {
                return true;
            }

            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }

            return null;
        });
    }

    /**
     * Shared hosting often ships with SESSION_DOMAIN=localhost which breaks login (419 / redirect loops).
     * Force safe session settings for the production hostname.
     */
    private function normalizeSessionConfig(string $host, bool $secure): void
    {
        $sessionDomain = config('session.domain');
        if (is_string($sessionDomain)) {
            $sessionDomain = trim($sessionDomain);
        }

        if ($sessionDomain === '' || strcasecmp((string) $sessionDomain, 'null') === 0) {
            config(['session.domain' => null]);
        } elseif (is_string($sessionDomain) && $sessionDomain !== '') {
            $normalized = ltrim($sessionDomain, '.');
            if ($host !== $normalized && ! str_ends_with($host, '.'.$normalized)) {
                config(['session.domain' => null]);
            }
        }

        if ($secure) {
            config(['session.secure' => true]);
        }

        // Production Alphawizz host: cookie sessions avoid unwritable storage / broken DB sessions.
        if (str_contains($host, 'developmentalphawizz.com') || str_contains($host, 'vyapto.')) {
            config([
                'session.driver' => 'cookie',
                'session.domain' => null,
                'session.secure' => true,
                'session.same_site' => 'lax',
                'session.path' => '/',
            ]);
        }
    }
}
