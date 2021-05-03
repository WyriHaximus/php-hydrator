<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

final class Package
{
    private string $label;
    private Cotton $cotton;

    /** @var array<\WyriHaximus\Tests\Hydrator\Cotton> */ //phpcs:disable
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
