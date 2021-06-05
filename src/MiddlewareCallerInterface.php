<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

interface MiddlewareCallerInterface extends HydratorInterface
{
    public function hydrator(): HydratorInterface;
}
