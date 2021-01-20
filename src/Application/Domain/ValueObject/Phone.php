<?php

declare(strict_types=1);

namespace App\Application\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Phone
{
    /**
     * @var string|null
     */
    private $value;

    public function __construct(?string $value)
    {
        if (!empty($value)) {
            Assert::minLength($value, 6);
            Assert::maxLength($value, 13);
            Assert::regex($value, '/^\+[0-9]{0,12}$/');
        }

        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
