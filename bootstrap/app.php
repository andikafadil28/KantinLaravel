<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: [
            'PHPSESSID',
            'LEGACYSESSID',
        ]);

        $middleware->validateCsrfTokens(except: [
            'legacy/validate/*',
            'legacy/proses/*',
            'legacy/excel_export/*',
            'legacy/inc/modal/*',
        ]);

        $middleware->alias([
            'kantin.auth' => \App\Http\Middleware\EnsureKantinAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
