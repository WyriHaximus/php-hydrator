<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

final class Cotton
{
    private int $id;
    private ?string $type = null;

    public function id(): int
    {
        return $this->id;
    }

    /** @phpstan-ignore-next-line */
    public function type(): ?string
    {
        return $this->type;
    }
}
