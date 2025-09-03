<?php

namespace Modules\RequireOauth\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\RequireOauth\Http\Middleware\CheckWemxOauth;

class RequireOauthServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'RequireOauth';

    protected string $moduleNameLower = 'requireoauth';

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerMiddleware($this->app['router']);
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    public function registerMiddleware($router): void
    {
        $router->pushMiddlewareToGroup('web', CheckWemxOauth::class);
    }
}
