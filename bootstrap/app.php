<?php

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
        // Behind a TLS-terminating reverse proxy (e.g. Render), trust its X-Forwarded-*
        // headers so URLs, redirects and secure cookies use https and the client IP.
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\PreventBackHistory::class,
        ]);
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // When the database cannot be reached (host down or powered off, network,
        // TLS or credential failure), show a "temporarily unavailable" page with
        // HTTP 503 instead of a generic 500. The exception is still logged in full.
        $exceptions->render(function (\PDOException $e, \Illuminate\Http\Request $request) {
            if (! preg_match('/SQLSTATE\[\w+\] \[(1045|2002|2003|2005|2006|2013)\]/', $e->getMessage())) {
                return null;
            }

            $message = 'Service temporarily unavailable. Please try again shortly.';
            $headers = ['Retry-After' => '60'];

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 503, $headers);
            }

            // Same view lookup Laravel uses for HTTP errors: resources/views/errors first.
            (new \Illuminate\Foundation\Exceptions\RegisterErrorViewPaths)();

            return response()->view('errors::503', [], 503, $headers);
        });
    })->create();
