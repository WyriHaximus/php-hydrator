<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator\Middleware;

use WyriHaximus\Hydrator\MiddlewareCallerInterface;
use WyriHaximus\Hydrator\MiddlewareInterface;
use WyriHaximus\Tests\Hydrator\Cotton;
use WyriHaximus\Tests\Hydrator\Package;

final class NestedEntityMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function hydrate(string $class, array $data, MiddlewareCallerInterface $next): object
    {
        if ($class === Package::class) {
            $data['cotton'] = $next->hydrator()->hydrate(Cotton::class, $data['cotton']);
        }

        return $next->hydrate($class, $data);
    }

    /**
     * @inheritDoc
     */
    public function extract(object $object, MiddlewareCallerInterface $next): array
    {
        $data = $next->extract($object);

        if ($object instanceof Package) {
            $data['cotton'] = $next->hydrator()->extract($data['cotton']);
        }

        return $data;
    }
}
