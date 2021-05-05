<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator\Middleware;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use WyriHaximus\Hydrator\Attribute\HydrateArray;
use WyriHaximus\Hydrator\MiddlewareCallerInterface;
use WyriHaximus\Hydrator\MiddlewareInterface;

use function array_map;
use function get_class;

final class NestedArrayEntityMiddleware implements MiddlewareInterface
{
    private Reader $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(string $class, array $data, MiddlewareCallerInterface $next): object
    {
        $reflectionClass = new ReflectionClass($class);

        foreach ($data as $key => $value) {
            if (! $reflectionClass->hasProperty($key)) {
                continue;
            }

            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionClass->getProperty($key), HydrateArray::class);

            if (! ($annotation instanceof HydrateArray)) {
                continue;
            }

            /**
             * @psalm-suppress InvalidArgument
             */
            $data[$key] = array_map(static fn (array $d): object => $next->hydrator()->hydrate($annotation->className(), $d), $data[$key]);
        }

        return $next->hydrate($class, $data);
    }

    /**
     * @inheritDoc
     */
    public function extract(object $object, MiddlewareCallerInterface $next): array
    {
        $data = $next->extract($object);

        $reflectionClass = new ReflectionClass(get_class($object));

        foreach ($data as $key => $value) {
            if (! $reflectionClass->hasProperty($key)) {
                continue;
            }

            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionClass->getProperty($key), HydrateArray::class);

            if (! ($annotation instanceof HydrateArray)) {
                continue;
            }

            /**
             * @psalm-suppress InvalidArgument
             */
            $data[$key] = array_map(static fn (object $d): array => $next->hydrator()->extract($d), $data[$key]);
        }

        return $data;
    }
}
