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
