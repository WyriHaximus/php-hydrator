<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

final class MiddlewareCaller implements MiddlewareCallerInterface
{
    private MiddlewareInterface $middleware;
    private MiddlewareCallerInterface $middlewareCaller;

    public function __construct(MiddlewareInterface $middleware, MiddlewareCallerInterface $middlewareCaller)
    {
        $this->middleware       = $middleware;
        $this->middlewareCaller = $middlewareCaller;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function hydrate(string $class, array $data): object
    {
        return $this->middleware->hydrate($class, $data, $this->middlewareCaller);
    }

    /**
     * @return array<string, mixed>
     */
    public function extract(object $object): array
    {
        return $this->middleware->extract($object, $this->middlewareCaller);
    }
}
