<?php

declare(strict_types=1);

namespace App\Clients\Domain\Client\ValueObject;

use Webmozart\Assert\Assert;

final class Status
{
    private const IN_WORK = 0;
    private const IN_BLACKLIST = 1;

    private static $names = [
        self::IN_WORK => 'in-work',
        self::IN_BLACKLIST => 'in-blacklist',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::IN_WORK,
            self::IN_BLACKLIST,
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
