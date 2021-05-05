<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator\Attribute;

/**
 * @Annotation
 */
final class Hydrate
{
    /** @var class-string */
    private string $className;

    /**
     * @param array<string, class-string> $values
     */
    public function __construct(array $values)
    {
        $this->className = $values['value'];
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }
}
