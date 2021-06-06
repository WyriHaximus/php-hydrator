<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

interface MiddlewareInterface
{
    /**
     * @param class-string<OM>     $class
     * @param array<string, mixed> $data
     *
     * @return OM
     *
     * @template OM of object
     */
    public function hydrate(string $class, array $data, MiddlewareCallerInterface $next): object;

    /**
     * @return array<string, mixed>
     */
    public function extract(object $object, MiddlewareCallerInterface $next): array;
}
