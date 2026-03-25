<?php

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;
use Illuminate\Support\Facades\View;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function (): void {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::get('/up', function () {
                $exception = null;

                try {
                    Event::dispatch(new DiagnosingHealth());
                } catch (Throwable $e) {
                    if (app()->hasDebugModeEnabled()) {
                        throw $e;
                    }

                    report($e);

                    $exception = $e->getMessage();
                }

                return response(View::file(base_path('vendor/laravel/framework/src/Illuminate/Foundation/resources/health-up.blade.php'), [
                    'exception' => $exception,
                ]), status: $exception ? 500 : 200);
            });

            require base_path('routes/health.php');

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
