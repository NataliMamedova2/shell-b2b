<?php

declare(strict_types=1);

namespace App\Users\Domain\User\ValueObject;

use Webmozart\Assert\Assert;

final class FullName
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        Assert::notEmpty($name, 'Name can\'t be empty.');
        Assert::lessThan($name, 255, 'Name must less than %2$s. Got: %s');

        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function getValue(): string
    {
        return $this->name;
    }

    public function equalTo($other): bool
    {
        return $other instanceof self && ((string) $this === (string) $other);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
