<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register exception handling callbacks.
     */
    public function register(): void
    {
        //
    }

    /**
     * Render exception into HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Apply only for API routes
        if ($request->is('api/*')) {

            // Token expired
            if ($exception instanceof TokenExpiredException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token has expired'
                ], 401);
            }

            // Token invalid
            if ($exception instanceof TokenInvalidException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token is invalid'
                ], 401);
            }

            // Token missing
            if ($exception instanceof JWTException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token is not provided'
                ], 401);
            }

            // Not authenticated
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle unauthenticated user.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized - Token required or invalid'
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}