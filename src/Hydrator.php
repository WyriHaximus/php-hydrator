<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

use function count;

final class Hydrator implements HydratorInterface
{
    /** @var array<MiddlewareInterface> */
    private array $middleware = [];

    private int $middlewareCount;

    public function __construct(MiddlewareInterface ...$middleware)
    {
        $this->middleware      = $middleware;
        $this->middlewareCount = count($this->middleware);
    }

    /**
     * @inheritDoc
     */
    public function hydrate(string $class, array $data): object
    {
        $stack = new HydratorMiddlewareCaller($this);
        for ($i = $this->middlewareCount - 1; $i >= 0; $i--) {
            $stack = new MiddlewareCaller($this, $this->middleware[$i], $stack);
        }

        return $stack->hydrate($class, $data);
    }

    /**
     * @inheritDoc
     */
    public function extract(object $object): array
    {
        $stack = new HydratorMiddlewareCaller($this);
        for ($i = 0; $i < $this->middlewareCount; $i++) {
            $stack = new MiddlewareCaller($this, $this->middleware[$i], $stack);
        }

        return $stack->extract($object);
    }
}
