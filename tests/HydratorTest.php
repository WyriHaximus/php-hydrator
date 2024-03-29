<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use SplQueue;
use WyriHaximus\Hydrator\Hydrator;
use WyriHaximus\Hydrator\Middleware\NestedArrayEntityMiddleware;
use WyriHaximus\Hydrator\Middleware\NestedEntityMiddleware;
use WyriHaximus\Tests\Hydrator\Middleware\CallRecordingMiddleware;
use WyriHaximus\TestUtilities\TestCase;

use function spl_object_hash;

final class HydratorTest extends TestCase
{
    /**
     * @test
     */
    public function basic(): void
    {
        $data = ['id' => 123, 'type' => null];

        $hydrator = new Hydrator();

        $cotton = $hydrator->hydrate(Cotton::class, $data);

        self::assertSame(123, $cotton->id());
        self::assertNull($cotton->type());

        $array = $hydrator->extract($cotton);

        self::assertSame($data, $array);
    }

    /**
     * @test
     */
    public function middleware(): void
    {
        $data = ['id' => 123, 'type' => null];

        $queue            = new SplQueue();
        $middlewareFirst  = new CallRecordingMiddleware($queue);
        $middlewareSecond = new CallRecordingMiddleware($queue);
        $middlewareThird  = new CallRecordingMiddleware($queue);
        $hydrator         = new Hydrator($middlewareFirst, $middlewareSecond, $middlewareThird);

        $cotton = $hydrator->hydrate(Cotton::class, $data);

        self::assertSame(123, $cotton->id());
        self::assertNull($cotton->type());
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
        $data      = ['label' => 'stamp', 'cotton' => ['id' => 123, 'type' => 'old'], 'cottons' => [['id' => 123], ['id' => 123], ['id' => 123], ['id' => 123]]];
        $dataAfter = ['label' => 'stamp', 'cotton' => ['id' => 123, 'type' => 'old'], 'cottons' => [['id' => 123, 'type' => null], ['id' => 123, 'type' => null], ['id' => 123, 'type' => null], ['id' => 123, 'type' => null]]];

        $reader   = new AnnotationReader();
        $hydrator = new Hydrator(new NestedEntityMiddleware($reader), new NestedArrayEntityMiddleware($reader));

        $package = $hydrator->hydrate(Package::class, $data);

        self::assertSame('stamp', $package->label());
        self::assertInstanceOf(Cotton::class, $package->cotton());
        self::assertSame(123, $package->cotton()->id());
        self::assertSame('old', $package->cotton()->type());
        self::assertCount(4, $package->cottons());
        foreach ($package->cottons() as $cotton) {
            self::assertSame(123, $cotton->id());
            self::assertNull($cotton->type());
        }

        $array = $hydrator->extract($package);
        self::assertSame($dataAfter, $array);
    }

    /**
     * @test
     */
    public function nestedNullableEntity(): void
    {
        $data = ['label' => 'stamp', 'cotton' => null, 'cottons' => []];

        $reader   = new AnnotationReader();
        $hydrator = new Hydrator(new NestedEntityMiddleware($reader), new NestedArrayEntityMiddleware($reader));

        $package = $hydrator->hydrate(Package::class, $data);

        self::assertSame('stamp', $package->label());
        self::assertNull($package->cotton());

        $array = $hydrator->extract($package);
        self::assertSame($data, $array);
    }
}
