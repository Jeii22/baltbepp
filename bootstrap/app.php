<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsSuperAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'isSuperAdmin' => IsSuperAdmin::class,
            'trackLastActive' => App\Http\Middleware\TrackLastActive::class,
        ]);
        // Apply to web group globally
        $middleware->web(append: [App\Http\Middleware\TrackLastActive::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
