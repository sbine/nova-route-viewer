<?php

namespace Sbine\RouteViewer\Tests;

use PHPUnit\Framework\TestCase;
use Sbine\RouteViewer\Column;

class ColumnTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_can_be_created_with_label_and_attribute(): void
    {
        $column = Column::make('My Label', 'my_attribute');

        $this->assertSame('My Label', $column->label);
        $this->assertSame('my_attribute', $column->attribute);
        $this->assertTrue($column->sortable);
    }

    public function test_it_can_be_made_non_sortable(): void
    {
        $column = Column::make('Label', 'attr')->sortable(false);

        $this->assertFalse($column->sortable);
    }

    public function test_it_converts_to_array(): void
    {
        $column = Column::make('Route', 'uri')->sortable(true);

        $this->assertSame([
            'label' => 'Route',
            'attribute' => 'uri',
            'sortable' => true,
        ], $column->toArray());
    }

    public function test_it_resolves_value_using_callback(): void
    {
        $column = Column::make('Domain', 'domain')
            ->using(fn ($route) => $route->getDomain());

        $route = new \Illuminate\Routing\Route('GET', 'test', fn () => null);

        $this->assertNull($column->resolve($route));
    }

    public function test_it_has_no_batch_resolver_by_default(): void
    {
        $column = Column::make('Label', 'attr');

        $this->assertFalse($column->hasBatchResolver());
    }

    public function test_it_resolves_batch_values(): void
    {
        $column = Column::make('Index', 'index')
            ->usingBatch(fn (array $routes) => array_map(fn ($r) => $r['uri'], $routes));

        $this->assertTrue($column->hasBatchResolver());

        $routes = [
            ['uri' => '/foo', 'as' => ''],
            ['uri' => '/bar', 'as' => ''],
        ];

        $this->assertSame(['/foo', '/bar'], $column->resolveBatch($routes));
    }

    public function test_resolve_returns_null_without_resolver(): void
    {
        $column = Column::make('Label', 'attr');

        $route = new \Illuminate\Routing\Route('GET', 'test', fn () => null);

        $this->assertNull($column->resolve($route));
    }

    public function test_resolve_batch_returns_empty_array_without_resolver(): void
    {
        $column = Column::make('Label', 'attr');

        $this->assertSame([], $column->resolveBatch([]));
    }
}
