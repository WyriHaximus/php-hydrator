<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\Hydrator\Middleware;

use SplQueue;
use WyriHaximus\Hydrator\MiddlewareCallerInterface;
use WyriHaximus\Hydrator\MiddlewareInterface;

use function spl_object_hash;

final class CallRecordingMiddleware implements MiddlewareInterface
{
    /** @var SplQueue<array<string>> */
    private SplQueue $queue;

    /**
     * @param SplQueue<array<string>> $queue
     */
    public function __construct(SplQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(string $class, array $data, MiddlewareCallerInterface $next): object
    {
        $this->queue->enqueue([spl_object_hash($this), __FUNCTION__]);

        return $next->hydrate($class, $data);
    }

    /**
     * @inheritDoc
     */
    public function extract(object $object, MiddlewareCallerInterface $next): array
    {
        $this->queue->enqueue([spl_object_hash($this), __FUNCTION__]);

        return $next->extract($object);
    }
}
