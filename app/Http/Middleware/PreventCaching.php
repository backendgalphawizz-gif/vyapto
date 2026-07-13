<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Some hosts run a full-page cache (e.g. LiteSpeed Cache) that caches redirect
 * responses and strips Set-Cookie headers. That breaks login entirely: a logged
 * in user hitting /admin/dashboard gets served a stale cached "redirect to login"
 * page instead of ever reaching PHP. This middleware tells any such cache to
 * never store auth-sensitive responses.
 */
class PreventCaching
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
