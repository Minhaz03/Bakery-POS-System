<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn (Request $request) =>
            $request->is('superadmin*') ? route('saas.login') : route('login')
        );
        
        $middleware->alias([
            'module' => \App\Http\Middleware\EnsureModuleActive::class,
            'subscribed' => \App\Http\Middleware\CheckSubscription::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            'dashboard/billing/payment/success',
            'dashboard/billing/payment/fail',
            'dashboard/billing/payment/cancel',
            'dashboard/billing/payment/ipn',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException|\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'You do not have sufficient permissions to perform this action.',
                ], 403);
            }

            if (!$request->isMethod('GET')) {
                return redirect()->back()->with('error', 'You do not have sufficient permissions to perform this action.');
            }

            return response()->view('errors.403', [
                'exception' => $e,
                'message' => 'You do not have sufficient permissions to perform this action.'
            ], 403);
        });
    })->create();
