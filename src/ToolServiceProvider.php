<?php

namespace Sbine\RouteViewer;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Sbine\RouteViewer\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            //
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authorize::class], 'route-viewer')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/route-viewer')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function registerTranslations()
    {
        $currentLocale = app()->getLocale();
        $localTranslationDir = __DIR__ . '/../resources/lang';
        $novaTranslationDir = resource_path('lang/vendor/route-viewer');

        Nova::translations($localTranslationDir . '/' . $currentLocale . '.json');
        Nova::translations($novaTranslationDir . '/' . $currentLocale . '.json');

        $this->loadTranslationsFrom($localTranslationDir, 'route-viewer');
        $this->loadJSONTranslationsFrom($localTranslationDir);
        $this->loadJSONTranslationsFrom($novaTranslationDir);

        $this->publishes([
            $localTranslationDir => $novaTranslationDir,
        ], 'translations');
    }
}
