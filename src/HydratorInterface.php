<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

interface HydratorInterface
{
    /**
     * @param class-string<O>      $class
     * @param array<string, mixed> $data
     *
     * @return O
     *
     * @template O of object
     */
    public function hydrate(string $class, array $data): object;

    /**
     * @return array<string, mixed>
     */
    public function extract(object $object): array;
}
