<?php

namespace Sbine\RouteViewer;

use Closure;
use Illuminate\Routing\Route;

class Filter
{
    public string $label;
    public bool $hiddenByDefault;
    protected Closure $callback;

    protected function __construct(string $label, Closure $callback, bool $hiddenByDefault = true)
    {
        $this->label = $label;
        $this->callback = $callback;
        $this->hiddenByDefault = $hiddenByDefault;
    }

    public static function make(string $label, callable $callback): static
    {
        return new static($label, Closure::fromCallable($callback));
    }

    public function shownByDefault(): static
    {
        $this->hiddenByDefault = false;

        return $this;
    }

    public function matches(Route $route): bool
    {
        return ($this->callback)($route);
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'hiddenByDefault' => $this->hiddenByDefault,
        ];
    }
}
