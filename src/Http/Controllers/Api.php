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

	        return [
	            'uri' => $route->uri,
	            'as' => $routeName,
	            'methods' => $route->methods,
	            'action' => $route->action['uses'] ?? '',
	            'middleware' => $route->action['middleware'] ?? [],
	        ];
	    });

	    return response()->json($routes);
    }
}
