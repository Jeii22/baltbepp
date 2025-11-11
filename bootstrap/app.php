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
        // Optionally trust proxies only when explicitly enabled via .env to fix HTTPS detection behind CDN/proxy
        $proxies = env('TRUST_PROXIES');
        if (!empty($proxies)) {
            // Accept '*' or comma-separated IPs
            \Illuminate\Support\Facades\Log::info('Trusting proxies', ['at' => $proxies]);
            $middleware->trustProxies(at: $proxies);
        }
    // (Reverted) Remove global trust of all proxies; rely on default behavior

        // Apply to web group globally
        $middleware->web(append: [
            App\Http\Middleware\TrackLastActive::class,
            App\Http\Middleware\CheckAccountLocked::class,
            App\Http\Middleware\IdleTimeout::class,
            App\Http\Middleware\SecurityHeaders::class,
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
                    ], 500);
                }

                // Don't override validation/authorization errors
                if ($exception instanceof ValidationException || 
                    $exception instanceof AuthorizationException || 
                    $exception instanceof HttpExceptionInterface) {
                    return null;
                }

                // Log detailed error, show generic message to user
                \Log::channel('security')->error('Unhandled Exception', [
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                    'user_id' => auth()->id(),
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                ]);

                // Original behavior: redirect back to home with a generic error message
                return back(fallback: url('/'))->with('error', 'An unexpected error occurred. Our team has been notified.');
            });
    })->create();
