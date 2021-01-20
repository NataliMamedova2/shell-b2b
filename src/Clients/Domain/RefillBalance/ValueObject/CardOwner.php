<?php

namespace App\Clients\Domain\RefillBalance\ValueObject;

use Webmozart\Assert\Assert;

final class CardOwner
{
    private const COMPANY = 2;
    private const PRIVATE = 3;

    private static $names = [
        self::COMPANY => 'company',
        self::PRIVATE => 'private',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::COMPANY,
            self::PRIVATE,
        ]);

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
