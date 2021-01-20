<?php

declare(strict_types=1);

namespace Domain\ValueObject;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use App\Application\Domain\Exception\InvalidIdentityException;

abstract class AbstractId
{
    protected $id;

    private function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $id
     *
     * @return static
     *
     * @throws InvalidIdentityException
     */
    public static function fromString(string $id)
    {
        try {
            return new static(Uuid::fromString($id));
        } catch (InvalidUuidStringException $exception) {
            throw new InvalidIdentityException($id);
        }
    }

    /**
     * @return static
     *
     * @throws \Exception
     */
    public static function next(): self
    {
        return new static(Uuid::uuid4());
    }

    public function equalTo(AbstractId $id): bool
    {
        return $this->getId() === $id->getId();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
