<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

final class MiddlewareCaller implements MiddlewareCallerInterface
{
    private HydratorInterface $hydrator;
    private MiddlewareInterface $middleware;
    private MiddlewareCallerInterface $middlewareCaller;

    public function __construct(HydratorInterface $hydrator, MiddlewareInterface $middleware, MiddlewareCallerInterface $middlewareCaller)
    {
        $this->hydrator         = $hydrator;
        $this->middleware       = $middleware;
        $this->middlewareCaller = $middlewareCaller;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(string $class, array $data): object
    {
        return $this->middleware->hydrate($class, $data, $this->middlewareCaller);
    }

    /**
     * @inheritDoc
     */
    public function extract(object $object): array
    {
        return $this->middleware->extract($object, $this->middlewareCaller);
    }

    public function hydrator(): HydratorInterface
    {
        return $this->hydrator;
    }
}
