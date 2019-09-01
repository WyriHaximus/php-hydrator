<?php declare(strict_types=1);

namespace WyriHaximus\Hydrator;

use GeneratedHydrator\Configuration;

final class Hydrator
{
    /** @var object[] */
    private $hydrators = [];

    public function hydrate(string $class, array $data): object
    {
        $hydrator = $this->getHydrator($class);

        return $hydrator->hydrate($data, new $class());
    }

    public function extract(object $object): array
    {
        $hydrator = $this->getHydrator(\get_class($object));

        return $hydrator->extract($object);
    }

    private function getHydrator(string $class): object
    {
        if (isset($this->hydrators[$class])) {
            return $this->hydrators[$class];
        }

        /**
         * @psalm-suppress MissingClosureReturnType
         * @psalm-suppress InvalidPropertyAssignmentValue
         */
        return $this->hydrators[$class] = (function (string $class) {
            $hydratorClass = (new Configuration($class))->createFactory()->getHydratorClass();

            /** @psalm-suppress InvalidStringClass */
            return new $hydratorClass();
        })($class);
    }
}
