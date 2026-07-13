<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Some hosting SAPIs (e.g. PHP-FPM behind cPanel/LiteSpeed) don't honor the
// display_errors=Off set in .htaccess (that only applies to mod_php/lsapi).
// If a stray notice/warning prints even a single byte before Laravel's own
// Response::send() runs, PHP implicitly flushes headers right then — and every
// later header()/setcookie() call (session cookies, CSRF, etc.) silently no-ops
// for the rest of the request. Buffering from the very first line guarantees
// nothing reaches the client before Laravel controls the headers.
ini_set('display_errors', '0');
ob_start();

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
