<?php

namespace App\Clients\Domain\RefillBalance\ValueObject;

final class OperationDate
{
    private $value;

    public function __construct(\DateTimeImmutable $date, \DateTimeInterface $time)
    {
        $hour = $time->format('H');
        $minute = $time->format('i');
        $second = $time->format('s');

        $this->value = $date->setTime($hour, $minute, $second);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getValue(): \DateTimeImmutable
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value->format('c');
    }
}
