<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Desabilita CSRF protection
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        $middleware->append(\App\Http\Middleware\CorsMiddleware::class);

        $middleware->append(\App\Http\Middleware\RespostaJsonMiddleware::class);

        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Middleware do Sanctum
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'check.token'=> \App\Http\Middleware\CheckTokenExpiracao::class
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

