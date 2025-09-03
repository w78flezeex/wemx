<?php

namespace Modules\Locales\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Locales\Console\LangCommand;
use Modules\Locales\Console\ScanTranslationsCommand;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Locales';

    protected string $moduleNameLower = 'locales';

    protected string $moduleNamespace = 'Modules\Locales\Http\Controllers';

    public function boot(): void
    {
        parent::boot();
        $this->commands([
            LangCommand::class,
            ScanTranslationsCommand::class,
        ]);
    }

    public function map(): void
    {
        $this->mapAdminRoutes();
        $this->registerConfig();
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Locales', '/Routes/web.php'));
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    protected function mapAdminRoutes(): void
    {
        Route::prefix('admin')
            ->middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Locales', '/Routes/admin.php'));
    }
}
