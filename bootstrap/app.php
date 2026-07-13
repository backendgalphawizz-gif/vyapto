<?php

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('portal', 'portal/*')) {
                return route('portal.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'jwt.verify' => JwtMiddleware::class,
            'app.user' => \App\Http\Middleware\EnsureAppUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $renderExpired = function (Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your session expired. Please refresh and try again.',
                ], 419);
            }

            // Never use redirect()->guest() here — it causes login↔login refresh loops.
            $loginUrl = $request->is('portal', 'portal/*')
                ? route('portal.login')
                : route('login');

            return redirect()
                ->to($loginUrl)
                ->setStatusCode(303)
                ->with('status', 'Your session expired. Please try logging in again.');
        };

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($renderExpired) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            return $renderExpired($request);
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) use ($renderExpired) {
            return $renderExpired($request);
        });

        $exceptions->reportable(function (TokenMismatchException $e) {
            \Illuminate\Support\Facades\Log::warning('CSRF token mismatch', [
                'url' => request()->fullUrl(),
                'host' => request()->getHost(),
                'secure' => request()->isSecure(),
                'session_driver' => config('session.driver'),
                'session_domain' => config('session.domain'),
                'has_session_cookie' => request()->hasCookie(config('session.cookie')),
            ]);
        });
    })->create();
