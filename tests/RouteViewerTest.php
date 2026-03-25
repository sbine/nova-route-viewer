<?php

namespace Sbine\RouteViewer\Tests;

use PHPUnit\Framework\TestCase;
use Sbine\RouteViewer\Column;
use Sbine\RouteViewer\RouteViewer;

/**
 * These tests require laravel/nova to be installed.
 *
 * @requires function \Laravel\Nova\Nova::registeredTools
 */
class RouteViewerTest extends TestCase
{
    protected function tearDown(): void
    {
        RouteViewer::flushColumns();
        parent::tearDown();
    }

    public function test_it_starts_with_no_custom_columns(): void
    {
        $this->assertSame([], RouteViewer::getColumns());
    }

    public function test_it_can_add_a_single_column(): void
    {
        $column = Column::make('Domain', 'domain');

        RouteViewer::addColumn($column);

        $this->assertCount(1, RouteViewer::getColumns());
        $this->assertSame($column, RouteViewer::getColumns()[0]);
    }

    public function test_it_can_add_multiple_columns(): void
    {
        $col1 = Column::make('Domain', 'domain');
        $col2 = Column::make('Rate Limit', 'rate_limit');

        RouteViewer::addColumns($col1, $col2);

        $this->assertCount(2, RouteViewer::getColumns());
    }

    public function test_it_can_flush_columns(): void
    {
        RouteViewer::addColumn(Column::make('Domain', 'domain'));

        $this->assertCount(1, RouteViewer::getColumns());

        RouteViewer::flushColumns();

        $this->assertSame([], RouteViewer::getColumns());
    }
}
