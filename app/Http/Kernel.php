<?php
protected $routeMiddleware = [
    // existing middleware
    'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
];