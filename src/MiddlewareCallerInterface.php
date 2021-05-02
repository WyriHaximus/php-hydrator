<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

interface MiddlewareCallerInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function hydrate(string $class, array $data): object;

    /**
     * @return array<string, mixed>
     */
    public function extract(object $object): array;

    public function hydrator(): Hydrator;
}
