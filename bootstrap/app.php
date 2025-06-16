<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\CompletePastSessions;




return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $app->routeMiddleware([
        //     'approved' => App\Http\Middleware\EnsureUserIsApproved::class,
        // ]);
        $middleware->alias([
            'approved' => EnsureUserIsApproved::class,
            'is.admin' => IsAdmin::class,
            'complete_past_sessions' => CompletePastSessions::class,
        ]);



    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
