<?php

namespace Laraditz\Lazada;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LazadaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lazada');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'lazada');
        // $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('lazada.php'),
            ], 'config');

            $this->publishMigrations();

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/lazada'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/lazada'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/lazada'),
            ], 'lang');*/

            // Registering package commands.
            if ($this->app->runningInConsole()) {
                $this->commands([
                    Console\RefreshTokenCommand::class,
                    Console\FlushExpiredTokenCommand::class,
                ]);
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'lazada');

        // Register the main class to use with the facade
        $this->app->singleton('lazada', function () {
            return new Lazada(
                region: config('lazada.region'),
                app_key: config('lazada.app_key'),
                app_secret: config('lazada.app_secret'),
                app_callback_url: config('lazada.app_callback_url'),
                sign_method: config('lazada.sign_method'),
                sandbox_mode: config('lazada.sandbox_mode'),
                seller_short_code: config('lazada.seller_short_code'),
            );
        });
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            Route::name('lazada.')->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('lazada.routes.prefix'),
            'middleware' => config('lazada.middleware'),
        ];
    }

    protected function publishMigrations()
    {
        $databasePath = __DIR__ . '/../database/migrations/';
        $migrationPath = database_path('migrations/');

        $files = array_diff(scandir($databasePath), array('.', '..'));
        $date = date('Y_m_d');
        $time = date('His');

        $migrationFiles = collect($files)
            ->mapWithKeys(function (string $file) use ($databasePath, $migrationPath, $date, &$time) {
                $filename = Str::replace(Str::substr($file, 0, 17), '', $file);

                $found = glob($migrationPath . '*' . $filename);
                $time = date("His", strtotime($time) + 1); // ensure in order
    
                return !!count($found) === true ? []
                    : [
                        $databasePath . $file => $migrationPath . $date . '_' . $time . $filename,
                    ];
            });

        if ($migrationFiles->isNotEmpty()) {
            $this->publishes($migrationFiles->toArray(), 'migrations');
        }
    }
}
