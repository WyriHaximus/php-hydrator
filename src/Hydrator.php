<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

use GeneratedHydrator\Configuration;

use function array_key_exists;
use function get_class;

final class Hydrator
{
    /** @var object[] */
    private array $hydrators = [];

    /**
     * @param array<string, mixed> $data
     */
    public function hydrate(string $class, array $data): object
    {
        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress InvalidStringClass
         */
        return $this->getHydrator($class)->hydrate($data, new $class());
    }

    /**
     * @return array<string, mixed>
     */
    public function extract(object $object): array
    {
        /**
         * @phpstan-ignore-next-line
         */
        return $this->getHydrator(get_class($object))->extract($object);
    }

    private function getHydrator(string $class): object
    {
        if (array_key_exists($class, $this->hydrators)) {
            return $this->hydrators[$class];
        }

        /**
         * @psalm-suppress MissingClosureReturnType
         * @psalm-suppress InvalidPropertyAssignmentValue
         */
        return $this->hydrators[$class] = (static function (string $class): object {
            /**
             * @phpstan-ignore-next-line
             * @psalm-suppress ArgumentTypeCoercion
             */
            $hydratorClass = (new Configuration($class))->createFactory()->getHydratorClass();

            /** @psalm-suppress InvalidStringClass */
            return new $hydratorClass();
        })($class);
    }
}
