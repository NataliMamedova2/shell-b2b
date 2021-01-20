<?php

declare(strict_types=1);

namespace App\Clients\Domain\User\ValueObject;

use Webmozart\Assert\Assert;

final class Username
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'Username can\'t be empty.');
        Assert::minLength($value, 5);
        Assert::maxLength($value, 30);
        Assert::regex($value, '/^[a-zA-Z\d_.]+$/i');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
