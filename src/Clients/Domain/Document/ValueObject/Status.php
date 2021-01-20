<?php

namespace App\Clients\Domain\Document\ValueObject;

use Webmozart\Assert\Assert;

final class Status
{
    private const FORMED_AUTOMATICALLY = 0;
    private const FORMED_BY_REQUEST = 1;

    private static $names = [
        self::FORMED_AUTOMATICALLY => 'formed-automatically',
        self::FORMED_BY_REQUEST => 'formed-by-request',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::FORMED_AUTOMATICALLY,
            self::FORMED_BY_REQUEST,
        ]);

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public static function getNames(): array
    {
        return self::$names;
    }

    public function getName(): string
    {
        return self::$names[$this->value];
    }

    public static function formedAuto(): self
    {
        return new self(self::FORMED_AUTOMATICALLY);
    }

    public static function formedByRequest(): self
    {
        return new self(self::FORMED_BY_REQUEST);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
