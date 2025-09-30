<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsSuperAdmin;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use App\Http\Middleware\HasAdminPrivileges;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'isSuperAdmin' => IsSuperAdmin::class,
            'isAdmin' => IsAdmin::class,
            'isUser' => IsUser::class,
            'hasAdminPrivileges' => HasAdminPrivileges::class,
            'trackLastActive' => App\Http\Middleware\TrackLastActive::class,
        ]);
        // Apply to web group globally
        $middleware->web(append: [App\Http\Middleware\TrackLastActive::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            return response()->view('errors.403', ['exception' => $e], 403);
        });
    })->create();
