<?php

declare(strict_types=1);

namespace App\Clients\Domain\Client\ValueObject;

use Webmozart\Assert\Assert;

final class Type
{
    private const PREPAYMENT = 0;
    private const CREDIT_LINE = 1;
    private const CREDIT = 2;

    private static $names = [
        self::PREPAYMENT => 'prepayment',
        self::CREDIT_LINE => 'credit-line',
        self::CREDIT => 'credit',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::PREPAYMENT,
            self::CREDIT_LINE,
            self::CREDIT,
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

    /**
     * @return array
     */
    public static function getNames(): array
    {
        return self::$names;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
