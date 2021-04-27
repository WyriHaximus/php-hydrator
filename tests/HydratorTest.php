<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

use WyriHaximus\Hydrator\Hydrator;
use WyriHaximus\TestUtilities\TestCase;

use function assert;

final class HydratorTest extends TestCase
{
    /**
     * @test
     */
    public function basic(): void
    {
        $data = ['id' => 123];

        $hydrator = new Hydrator();

        $cotton = $hydrator->hydrate(Cotton::class, $data);
        assert($cotton instanceof Cotton);

        self::assertSame(123, $cotton->getId());

        $array = $hydrator->extract($cotton);

        self::assertSame($data, $array);
    }
}
