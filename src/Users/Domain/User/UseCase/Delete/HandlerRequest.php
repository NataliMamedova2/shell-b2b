<?php

namespace App\Users\Domain\User\UseCase\Delete;

final class HandlerRequest implements \Domain\Interfaces\HandlerRequest
{
    /**
     * @var string
     */
    private $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function extract(array $properties = []): array
    {
        $state = get_object_vars($this);

        foreach (array_keys($state) as $property) {
            $getter = 'get'.ucfirst($property);
            if (method_exists($this, $getter)) {
                $state[$property] = $this->{$getter}();
            }
        }

        if (empty($properties)) {
            return $state;
        }

        $rawArray = array_fill_keys($properties, null);

        return array_intersect_key($state, $rawArray);
    }
}
