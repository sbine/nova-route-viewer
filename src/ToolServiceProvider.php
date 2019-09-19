<?php

namespace Sbine\RouteViewer;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'route-viewer');

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

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/route-viewer')
            ->group(__DIR__.'/../routes/api.php');
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

        Nova::translations(__DIR__.'/../resources/lang/'.$currentLocale.'.json');
        Nova::translations(resource_path('lang/vendor/route-viewer/'.$currentLocale.'.json'));

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'route-viewer');
        $this->loadJSONTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadJSONTranslationsFrom(resource_path('lang/vendor/route-viewer'));
    }
}
