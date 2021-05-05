<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

use WyriHaximus\Hydrator\Attribute\Hydrate;
use WyriHaximus\Hydrator\Attribute\HydrateArray;

final class Package
{
    private string $label;

    /** @Hydrate(Cotton::class) */
    private Cotton $cotton;

    /**
     * @var array<Cotton>
     * @HydrateArray(Cotton::class)
     */
    private array $cottons;

    public function label(): string
    {
        return $this->label;
    }

    public function cotton(): Cotton
    {
        return $this->cotton;
    }

    /**
     * @return array<Cotton>
     */
    public function cottons(): array
    {
        return $this->cottons;
    }
}
