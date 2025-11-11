<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsSuperAdmin;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use App\Http\Middleware\HasAdminPrivileges;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
            'checkLocked' => App\Http\Middleware\CheckAccountLocked::class,
            'role' => App\Http\Middleware\RoleMiddleware::class,
            'requireAdmin2FA' => App\Http\Middleware\RequireAdminTwoFactor::class,
            'password.confirm' => App\Http\Middleware\RequirePasswordConfirmation::class,
            'idleTimeout' => App\Http\Middleware\IdleTimeout::class,
            'securityHeaders' => App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        // CRITICAL FIX: Trust all proxies to fix HTTPS detection behind hosting providers/CDN
        // This prevents redirect loops caused by session cookie issues
        $middleware->trustProxies(at: '*');

        // Apply to web group globally
        $middleware->web(append: [
            App\Http\Middleware\SecurityHeaders::class,
            App\Http\Middleware\TrackLastActive::class,
            App\Http\Middleware\CheckAccountLocked::class,
            App\Http\Middleware\IdleTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log all exceptions with minimal user exposure
        $exceptions->reportable(function (Throwable $e) {
            \Log::channel('security')->error('Application Error', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'url' => request()->fullUrl(),
                'ip' => request()->ip(),
            ]);
        });

        $exceptions->renderable(function (ValidationException $exception, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            $firstMessage = collect($exception->errors())->flatten()->first()
                ?? 'Please review the form and try again.';

            return back(fallback: url('/'))
                ->withInput($request->all())
                ->withErrors($exception->errors(), $exception->errorBag)
                ->with('error', $firstMessage);
        });

        $exceptions->renderable(function (AuthorizationException $exception, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            $message = $exception->getMessage() ?: 'You do not have permission to access this area.';

            return back(fallback: url('/'))->with('error', $message);
        });

        $exceptions->renderable(function (HttpExceptionInterface $exception, $request) {
            if ($exception->getStatusCode() !== 403 || $request->expectsJson()) {
                return null;
            }

            $message = $exception->getMessage() ?: 'You do not have permission to access this area.';

            return back(fallback: url('/'))->with('error', $message);
        });

        // Catch-all for unexpected errors (hide stack traces, show user-friendly message)
            $exceptions->renderable(function (Throwable $exception, $request) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'An error occurred. Please try again later.',
                        'error' => app()->hasDebugModeEnabled() ? $exception->getMessage() : null,
                    ], 500);
                }

                if ($exception instanceof ValidationException ||
                    $exception instanceof AuthorizationException ||
                    $exception instanceof HttpExceptionInterface) {
                    return null;
                }

                \Log::error('Unhandled Exception (500 page rendered)', [
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'user_id' => auth()->id(),
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                ]);

                return response()->view('errors.500', [
                    'message' => app()->hasDebugModeEnabled() ? $exception->getMessage() : 'An unexpected error occurred.'
                ], 500);
            });
    })->create();
