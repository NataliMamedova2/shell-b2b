<?php

namespace App\Clients\Domain\Card\ValueObject;

use Webmozart\Assert\Assert;

final class MoneyLimits
{
    private const DAY_MIN_LIMIT = 1;

    /**
     * @var int
     */
    private $day;

    /**
     * @var int
     */
    private $week;

    /**
     * @var int
     */
    private $month;

    public function __construct(string $day, string $week, string $month)
    {
        $this->validate('dayLimit', $day, self::DAY_MIN_LIMIT);
        $this->validate('weekLimit', $week, self::DAY_MIN_LIMIT);
        $this->validate('monthLimit', $month, self::DAY_MIN_LIMIT);

        $this->day = (int) $day * 100;
        $this->week = (int) $week * 100;
        $this->month = (int) $month * 100;
    }

    private function validate(string $propertyName, $value, int $min): void
    {
        Assert::maxLength($value, 14);
        $message = sprintf('"%s" value should be greater than %s. Got: %s', $propertyName, $min, $value);
        Assert::greaterThanEq($value, $min, $message);
    }

    public function getDayLimit(): int
    {
        return (int) $this->day;
    }

    public function getWeekLimit(): int
    {
        return (int) $this->week;
    }

    public function getMonthLimit(): int
    {
        return (int) $this->month;
    }

    public function equals(self $other): bool
    {
        return $this->getDayLimit() === $other->getDayLimit() &&
            $this->getWeekLimit() === $other->getWeekLimit() &&
            $this->getMonthLimit() === $other->getMonthLimit();
    }
}
