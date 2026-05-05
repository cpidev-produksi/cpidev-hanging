<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
            $middleware->alias([
            'nocache' => \App\Http\Middleware\NoCache::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'supervisor' => \App\Http\Middleware\SupervisorOnly::class,
            'superadmin' => \App\Http\Middleware\RoleMiddleware::class,
            'perm' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
