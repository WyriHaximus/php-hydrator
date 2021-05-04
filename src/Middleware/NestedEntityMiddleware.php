<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator\Middleware;

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use ReflectionClass;
use WyriHaximus\Hydrator\MiddlewareCallerInterface;
use WyriHaximus\Hydrator\MiddlewareInterface;

use function array_map;
use function class_exists;
use function count;
use function current;
use function get_class;
use function is_string;
use function ltrim;

final class NestedEntityMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function hydrate(string $class, array $data, MiddlewareCallerInterface $next): object
    {
        $reflectionClass = new ReflectionClass($class);

        foreach ($data as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            if (!$reflectionClass->hasProperty($key)) {
                continue;
            }
            $reflectionProperty = $reflectionClass->getProperty($key);

            $type = $reflectionProperty->getType();
            if ($type === null) {
                continue;
            }

            $cc = ltrim((string) $type, '\\');

            if (! class_exists($cc)) {
                continue;
            }

            $data[$key] = $next->hydrator()->hydrate($cc, $data[$key]);
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
            if (! is_string($key)) {
                continue;
            }

            if (!$reflectionClass->hasProperty($key)) {
                continue;
            }
            $reflectionProperty = $reflectionClass->getProperty($key);

            $type = $reflectionProperty->getType();
            if ($type === null) {
                continue;
            }

            $cc = ltrim((string) $type, '\\');

            if (! class_exists($cc)) {
                continue;
            }

            $data[$key] = $next->hydrator()->extract($data[$key]);
        }

        return $data;
    }
}
