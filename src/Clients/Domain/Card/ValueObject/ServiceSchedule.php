<?php

namespace App\Clients\Domain\Card\ValueObject;

use Webmozart\Assert\Assert;

final class ServiceSchedule
{
    private static $names = [
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
        'sun',
    ];

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::length($value, 7);
        Assert::allOneOf(str_split($value), ['0', '1']);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function createByNames(array $names): self
    {
        Assert::allOneOf($names, self::$names);

        $values = [];
        for ($i = 0; $i < count(self::$names); ++$i) {
            $values[$i] = 0;
            if (in_array(self::$names[$i], $names)) {
                $values[$i] = 1;
            }
        }

        return new self(implode('', $values));
    }

    public static function getNames(): array
    {
        return self::$names;
    }

    public function equals(self $other): bool
    {
        return (string) $this === (string) $other;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
