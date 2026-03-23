<?php

namespace Sbine\RouteViewer\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Sbine\RouteViewer\RouteViewer;

class Api
{
    /**
     * Return all the registered routes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoutes()
    {
        $rawRoutes = collect(Route::getRoutes());

        $routes = $rawRoutes->map(function ($route) {
            $routeName = $route->action['as'] ?? '';
            if (Str::endsWith($routeName, '.')) {
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
                'action' => is_string($route->action['uses'] ?? '') ? ($route->action['uses'] ?? '') : 'Closure',
                'middleware' => $routeMiddleware,
            ];
        })->values()->all();

        $customColumns = RouteViewer::getColumns();

        foreach ($customColumns as $column) {
            if ($column->hasBatchResolver()) {
                $values = $column->resolveBatch($routes);
                foreach ($values as $index => $value) {
                    $routes[$index][$column->attribute] = $value;
                }
            } else {
                foreach ($rawRoutes->values() as $index => $rawRoute) {
                    $routes[$index][$column->attribute] = $column->resolve($rawRoute);
                }
            }
        }

        $columns = array_merge(static::builtInColumns(), array_map(fn ($column) => $column->toArray(), $customColumns));

        return response()->json([
            'columns' => $columns,
            'routes' => $routes,
        ]);
    }

    protected static function builtInColumns(): array
    {
        return [
            ['label' => 'Route', 'attribute' => 'uri', 'sortable' => true],
            ['label' => 'Name', 'attribute' => 'as', 'sortable' => true],
            ['label' => 'Methods', 'attribute' => 'methods', 'sortable' => true],
            ['label' => 'Action', 'attribute' => 'action', 'sortable' => true],
            ['label' => 'Middleware', 'attribute' => 'middleware', 'sortable' => true],
        ];
    }
}
