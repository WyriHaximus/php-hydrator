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

final class NestedArrayEntityMiddleware implements MiddlewareInterface
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

            $reflectionProperty = $reflectionClass->getProperty($key);
            if ($reflectionProperty === null) {
                continue;
            }

            $docblock = $reflectionProperty->getDocComment();
            if (! is_string($docblock)) {
                continue;
            }

            $constExprParser = new ConstExprParser();
            $tokens          = new TokenIterator((new Lexer())->tokenize($docblock));
            $varTag          = (new PhpDocParser(new TypeParser($constExprParser), $constExprParser))->parse($tokens)->getVarTagValues();
            if (count($varTag) === 0) {
                continue;
            }

            $varTag = current($varTag);
            if ((string) $varTag->type->type !== 'array') {
                continue;
            }

            if (count($varTag->type->genericTypes) === 0) {
                continue;
            }

            $cc = ltrim((string) current($varTag->type->genericTypes), '\\');

            if (! class_exists($cc)) {
                continue;
            }

            $data[$key] = array_map(static fn (array $d): object => $next->hydrator()->hydrate($cc, $d), $data[$key]);
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

            $reflectionProperty = $reflectionClass->getProperty($key);
            if ($reflectionProperty === null) {
                continue;
            }

            $docblock = $reflectionProperty->getDocComment();
            if (! is_string($docblock)) {
                continue;
            }

            $constExprParser = new ConstExprParser();
            $tokens          = new TokenIterator((new Lexer())->tokenize($docblock));
            $varTag          = (new PhpDocParser(new TypeParser($constExprParser), $constExprParser))->parse($tokens)->getVarTagValues();
            if (count($varTag) === 0) {
                continue;
            }

            $varTag = current($varTag);
            if ((string) $varTag->type->type !== 'array') {
                continue;
            }

            if (count($varTag->type->genericTypes) === 0) {
                continue;
            }

            $cc = ltrim((string) current($varTag->type->genericTypes), '\\');

            if (! class_exists($cc)) {
                continue;
            }

            $data[$key] = array_map(static fn (object $d): array => $next->hydrator()->extract($d), $data[$key]);
        }

        return $data;
    }
}
