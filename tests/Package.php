<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

final class Package
{
    private string $label;
    private Cotton $cotton;

    public function label(): string
    {
        return $this->label;
    }

    public function cotton(): Cotton
    {
        return $this->cotton;
    }
}
