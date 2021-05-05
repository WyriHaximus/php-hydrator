<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator\Middleware;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use WyriHaximus\Hydrator\Attribute\Hydrate;
use WyriHaximus\Hydrator\MiddlewareCallerInterface;
use WyriHaximus\Hydrator\MiddlewareInterface;

use function get_class;

final class NestedEntityMiddleware implements MiddlewareInterface
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

            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionClass->getProperty($key), Hydrate::class);

            if (! ($annotation instanceof Hydrate)) {
                continue;
            }

            $data[$key] = $next->hydrator()->hydrate($annotation->className(), $data[$key]);
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

            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionClass->getProperty($key), Hydrate::class);

            if (! ($annotation instanceof Hydrate)) {
                continue;
            }

            /**
             * @psalm-suppress PossiblyInvalidArgument
             */
            $data[$key] = $next->hydrator()->extract($data[$key]);
        }

        return $data;
    }
}
