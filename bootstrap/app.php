<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Force laravel api to always render a json response
        // $middleware->append(\App\Http\Middleware\ReturnJsonResponseMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Force laravel api to always render a json response
        // $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
        //     return $request->is('api/*') || $request->expectsJson();
        // });
    })->create();
