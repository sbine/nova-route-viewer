<?php

namespace Sbine\RouteViewer;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class RouteViewer extends Tool
{
    protected static array $columns = [];

    /** @var Filter[] */
    protected static array $filters = [];

    public static function addColumn(Column $column): void
    {
        static::$columns[] = $column;
    }

    public static function addColumns(Column ...$columns): void
    {
        array_push(static::$columns, ...$columns);
    }

    public static function getColumns(): array
    {
        return static::$columns;
    }

    public static function flushColumns(): void
    {
        static::$columns = [];
    }

    public static function addFilter(Filter $filter): void
    {
        static::$filters[] = $filter;
    }

    public static function addFilters(Filter ...$filters): void
    {
        array_push(static::$filters, ...$filters);
    }

    /** @return Filter[] */
    public static function getFilters(): array
    {
        return static::$filters;
    }

    public static function flushFilters(): void
    {
        static::$filters = [];
    }

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('route-viewer', __DIR__.'/../dist/js/tool.js');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuSection::make(__('Route Viewer'))
            ->path('/route-viewer')
            ->icon('route-viewer');
    }
}
