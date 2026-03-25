<?php

namespace Sbine\RouteViewer;

use Closure;
use Illuminate\Routing\Route;

class Column
{
    public string $label;

    public string $attribute;

    public bool $sortable = true;

    protected ?Closure $resolver = null;

    protected ?Closure $batchResolver = null;

    protected function __construct(string $label, string $attribute)
    {
        $this->label = $label;
        $this->attribute = $attribute;
    }

    public static function make(string $label, string $attribute): static
    {
        return new static($label, $attribute);
    }

    public function using(callable $resolver): static
    {
        $this->resolver = Closure::fromCallable($resolver);

        return $this;
    }

    public function usingBatch(callable $resolver): static
    {
        $this->batchResolver = Closure::fromCallable($resolver);

        return $this;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function hasBatchResolver(): bool
    {
        return $this->batchResolver !== null;
    }

    public function resolve(Route $route): mixed
    {
        return $this->resolver ? ($this->resolver)($route) : null;
    }

    public function resolveBatch(array $routeData): array
    {
        return $this->batchResolver ? ($this->batchResolver)($routeData) : [];
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'attribute' => $this->attribute,
            'sortable' => $this->sortable,
        ];
    }
}
