<?php

namespace Modules\AAIOGateway\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route; // <-- Добавлено

class AAIOGatewayServiceProvider extends ServiceProvider
{
    protected $moduleName = 'AAIOGateway';
    protected $moduleNameLower = 'aaiogateway'; // Обычно совпадает с alias из module.json

    public function boot()
    {
        $this->registerRoutes(); // <-- Добавлено
        // Другая логика, если есть
    }

    public function register()
    {
        //
    }

     /**
     * Регистрирует веб-маршруты для модуля.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::middleware('web') // Или 'api' если callback без web middleware
            ->prefix('aaio') // Префикс URL
            ->group(__DIR__ . '/../Routes/web.php'); // Путь к файлу маршрутов
    }

    // ... остальные методы
}