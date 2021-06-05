<?php

declare(strict_types=1);

namespace WyriHaximus\Hydrator;

use GeneratedHydrator\Configuration;

use function array_key_exists;
use function get_class;

final class HydratorMiddlewareCaller implements MiddlewareCallerInterface
{
    /** @var object[] */
    private array $hydrators = [];

    private HydratorInterface $hydrator;

    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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

    public function hydrator(): HydratorInterface
    {
        return $this->hydrator;
    }
}
