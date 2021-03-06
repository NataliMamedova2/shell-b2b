<?php

declare(strict_types=1);

namespace App\Feedback\Domain\Feedback\ValueObject;

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
        Assert::maxLength($name, 50);

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

    public function __toString(): string
    {
        return $this->getValue();
    }
}
