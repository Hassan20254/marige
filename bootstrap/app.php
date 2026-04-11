<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.session' => \App\Http\Middleware\CheckUserSession::class,
            'check.admin' => \App\Http\Middleware\CheckAdminSession::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'login',
            'user/login',
            'admin/login',
            'register',
            'auth/google/callback',
            'complete-google-registration',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
