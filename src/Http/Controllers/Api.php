<?php

namespace Sbine\RouteViewer\Http\Controllers;

use Illuminate\Support\Facades\Route;

class Api
{
    /**
     * Return all the registered routes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoutes()
    {
        $routes = collect(Route::getRoutes())->map(function ($route, $index) {
            $routeName = $route->action['as'] ?? '';
            if (ends_with($routeName, '.')) {
                $routeName = '';
            }

            $routeMiddleware = $route->action['middleware'] ?? [];
            if (! is_array($routeMiddleware)) {
                $routeMiddleware = [$routeMiddleware];
            }

            return [
                'uri' => $route->uri,
                'as' => $routeName,
                'methods' => $route->methods,
                'action' => $route->action['uses'] ?? '',
                'middleware' => $routeMiddleware,
            ];
        });

        return response()->json($routes);
    }
}
