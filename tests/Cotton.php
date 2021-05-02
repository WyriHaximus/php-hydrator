<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

final class Cotton
{
    private int $id;

    public function id(): int
    {
        return $this->id;
    }
}
