<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

use SplQueue;
use WyriHaximus\Hydrator\Hydrator;
use WyriHaximus\Tests\Hydrator\Middleware\CallRecordingMiddleware;
use WyriHaximus\Tests\Hydrator\Middleware\NestedEntityMiddleware;
use WyriHaximus\TestUtilities\TestCase;

use function assert;
use function spl_object_hash;

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

        self::assertSame(123, $cotton->id());

        $array = $hydrator->extract($cotton);

        self::assertSame($data, $array);
    }

    /**
     * @test
     */
    public function middleware(): void
    {
        $data = ['id' => 123];

        $queue            = new SplQueue();
        $middlewareFirst  = new CallRecordingMiddleware($queue);
        $middlewareSecond = new CallRecordingMiddleware($queue);
        $middlewareThird  = new CallRecordingMiddleware($queue);
        $hydrator         = new Hydrator($middlewareFirst, $middlewareSecond, $middlewareThird);

        $cotton = $hydrator->hydrate(Cotton::class, $data);
        assert($cotton instanceof Cotton);

        self::assertSame(123, $cotton->id());
        $array = $hydrator->extract($cotton);
        self::assertSame($data, $array);

        $queueArray = [];
        do {
            $queueArray[] = $queue->dequeue();
        } while (! $queue->isEmpty());

        self::assertSame([
            [spl_object_hash($middlewareFirst), 'hydrate'],
            [spl_object_hash($middlewareSecond), 'hydrate'],
            [spl_object_hash($middlewareThird), 'hydrate'],
            [spl_object_hash($middlewareThird), 'extract'],
            [spl_object_hash($middlewareSecond), 'extract'],
            [spl_object_hash($middlewareFirst), 'extract'],
        ], $queueArray);
    }

    /**
     * @test
     */
    public function nestedEntity(): void
    {
        $data = ['label' => 'stamp', 'cotton' => ['id' => 123]];

        $queue    = new SplQueue();
        $hydrator = new Hydrator(new NestedEntityMiddleware());

        $package = $hydrator->hydrate(Package::class, $data);
        assert($package instanceof Package);

        self::assertSame('stamp', $package->label());
        self::assertSame(123, $package->cotton()->id());
        $array = $hydrator->extract($package);
        self::assertSame($data, $array);
    }
}
