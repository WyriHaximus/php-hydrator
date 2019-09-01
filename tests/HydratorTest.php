<?php declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\Hydrator\Hydrator;

/**
 * @internal
 */
final class HydratorTest extends AsyncTestCase
{
    /**
     * @test
     */
    public function basic(): void
    {
        $data = [
            'id' => 123,
        ];

        $hydrator = new Hydrator();

        /** @var Cotton $cotton */
        $cotton = $hydrator->hydrate(Cotton::class, $data);

        self::assertSame(123, $cotton->getId());

        $array = $hydrator->extract($cotton);

        self::assertSame($data, $array);
    }
}
