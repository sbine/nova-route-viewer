# Laravel Nova Route Viewer

This [Nova](https://nova.laravel.com/) tool adds a route viewer section to the Nova sidebar.

It's like `php artisan route:list` for your browser. Supports sorting and filtering.

![screenshot of Laravel Nova Route Viewer tool](https://sarabine.com/i//Laravel-Nova-Route-Viewer-Tool.png)

## Installation

Install via [Composer](https://getcomposer.org/):
```
composer require sbine/route-viewer
```

Register the tool in `app/Providers/NovaServiceProvider`:

```php
public function tools()
{
    return [
        new \Sbine\RouteViewer\RouteViewer,
    ];
}
```

You can customize the translations by publishing them to your local folder `resources/lang/vendor/route-viewer`:

```
php artisan vendor:publish --provider="Sbine\RouteViewer\ToolServiceProvider"
```

## Custom Columns

You can add custom columns to the route viewer table. Register them in your `NovaServiceProvider` or `AppServiceProvider`:

```php
use Sbine\RouteViewer\RouteViewer;
use Sbine\RouteViewer\Column;

// Simple per-route resolver
RouteViewer::addColumn(
    Column::make('Domain', 'domain')
        ->using(fn (\Illuminate\Routing\Route $route) => $route->getDomain() ?? '—')
);

// Batch resolver (efficient for DB lookups, avoids N+1)
RouteViewer::addColumn(
    Column::make('Hits', 'hits')
        ->usingBatch(function (array $routes): array {
            $hits = DB::table('route_hits')
                ->whereIn('uri', array_column($routes, 'uri'))
                ->groupBy('uri')
                ->pluck(DB::raw('count(*)'), 'uri');

            return array_map(fn ($r) => $hits[$r['uri']] ?? 0, $routes);
        })
);

// Non-sortable column
RouteViewer::addColumn(
    Column::make('Notes', 'notes')
        ->using(fn ($route) => config("route-notes.{$route->uri}", ''))
        ->sortable(false)
);
```

Custom columns are automatically searchable and sortable. Use `->sortable(false)` to opt out of sorting.

## Custom Filters

You can register custom route filters that add toggle checkboxes to the toolbar. Routes matching a filter are hidden by default; clicking the checkbox reveals them.

```php
use Sbine\RouteViewer\RouteViewer;
use Sbine\RouteViewer\Filter;

// Hide Nova routes by default (checkbox toggles visibility)
RouteViewer::addFilter(
    Filter::make('Nova', function (\Illuminate\Routing\Route $route) {
        $middleware = (array) ($route->action['middleware'] ?? []);
        $action = $route->action['uses'] ?? '';

        return in_array('nova', $middleware)
            || in_array('nova:api', $middleware)
            || (is_string($action) && str_starts_with($action, 'Laravel\\Nova'));
    })
);

// Hide Horizon routes by default
RouteViewer::addFilter(
    Filter::make('Horizon', fn (\Illuminate\Routing\Route $route) =>
        is_string($route->action['uses'] ?? '') && str_starts_with($route->action['uses'], 'Laravel\\Horizon')
    )
);

// Show Sanctum routes by default (opt-in visibility)
RouteViewer::addFilter(
    Filter::make('Sanctum', fn (\Illuminate\Routing\Route $route) =>
        is_string($route->action['uses'] ?? '') && str_starts_with($route->action['uses'], 'Laravel\\Sanctum')
    )->shownByDefault()
);
```

Filters replace the previously hardcoded Nova/Passport/Horizon checkboxes. Register your own in `NovaServiceProvider` or `AppServiceProvider`.

## Contributing

After updating frontend assets, rebuild for production:

```
npm install
npm run prod
```

> **Note:** This project uses Laravel Mix 6, which requires Node.js 20 LTS. It is not compatible with Node.js 25+.
