<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/routes', function () {
    $routes = collect(Route::getRoutes())->map(function ($route, $index) {
        $routeName = $route->action['as'] ?? '';
        if (ends_with($routeName, '.')) {
            $routeName = '';
        }

        return [
            'uri' => $route->uri,
            'as' => $routeName,
            'methods' => $route->methods,
            'action' => $route->action['uses'],
            'middleware' => $route->action['middleware'],
        ];
    });

    return response()->json($routes);
});
