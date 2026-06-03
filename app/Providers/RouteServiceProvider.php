<?php

namespace App\Providers;

use App\Models\ProductEvis;
use App\Models\ReportEvis;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/menu';

    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureRouteModelBindings();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        // Rate limiting optional
    }

    protected function configureRouteModelBindings(): void
    {
        Route::model('productEvis', ProductEvis::class);
        Route::model('reportEvis', ReportEvis::class);
    }
}