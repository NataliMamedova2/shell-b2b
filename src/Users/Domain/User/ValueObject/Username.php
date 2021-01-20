<?php

declare(strict_types=1);

namespace App\Users\Domain\User\ValueObject;

use Webmozart\Assert\Assert;

final class Username
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        Assert::regex($name, '/^[a-z\d_.]+$/i');
        Assert::notEmpty($name, 'Username can\'t be empty.');
        Assert::lengthBetween($name, 4, 255, 'Username must be between %2$s and %3$s characters');

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
