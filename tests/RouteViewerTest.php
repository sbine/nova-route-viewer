<?php

namespace Sbine\RouteViewer\Tests;

use PHPUnit\Framework\TestCase;
use Sbine\RouteViewer\Column;

/**
 * These tests require laravel/nova to be installed.
 *
 * @requires function \Laravel\Nova\Nova::registeredTools
 */
class RouteViewerTest extends TestCase
{
    protected function tearDown(): void
    {
        \Sbine\RouteViewer\RouteViewer::flushColumns();
        parent::tearDown();
    }

    public function test_it_starts_with_no_custom_columns(): void
    {
        $this->assertSame([], \Sbine\RouteViewer\RouteViewer::getColumns());
    }

    public function test_it_can_add_a_single_column(): void
    {
        $column = Column::make('Domain', 'domain');

        \Sbine\RouteViewer\RouteViewer::addColumn($column);

        $this->assertCount(1, \Sbine\RouteViewer\RouteViewer::getColumns());
        $this->assertSame($column, \Sbine\RouteViewer\RouteViewer::getColumns()[0]);
    }

    public function test_it_can_add_multiple_columns(): void
    {
        $col1 = Column::make('Domain', 'domain');
        $col2 = Column::make('Rate Limit', 'rate_limit');

        \Sbine\RouteViewer\RouteViewer::addColumns($col1, $col2);

        $this->assertCount(2, \Sbine\RouteViewer\RouteViewer::getColumns());
    }

    public function test_it_can_flush_columns(): void
    {
        \Sbine\RouteViewer\RouteViewer::addColumn(Column::make('Domain', 'domain'));

        $this->assertCount(1, \Sbine\RouteViewer\RouteViewer::getColumns());

        \Sbine\RouteViewer\RouteViewer::flushColumns();

        $this->assertSame([], \Sbine\RouteViewer\RouteViewer::getColumns());
    }
}
