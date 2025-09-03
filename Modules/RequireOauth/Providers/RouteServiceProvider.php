<?php

namespace Modules\RequireOauth\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\RequireOauth\Http\Controllers';

    public function map(): void
    {
        $this->mapAdminRoutes();
    }

    protected function mapAdminRoutes(): void
    {
        Route::prefix('admin')->middleware(['web', 'auth'])
            ->namespace($this->moduleNamespace)
            ->group(module_path('RequireOauth', '/Routes/admin.php'));
    }
}
