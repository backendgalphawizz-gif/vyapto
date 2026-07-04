<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (in_array((int) $user->role_id, [1, 2], true)) {
            return redirect()->route('admin.dashboard');
        }

        if ((int) $user->status !== 1) {
            abort(403, 'Your account is inactive. Please contact admin.');
        }

        return $next($request);
    }
}
