<?php

namespace Tests\Unit\Clients\Domain\ClientInfo\ValueObject;

use App\Clients\Domain\ClientInfo\ValueObject\LastTransactionDate;
use PHPUnit\Framework\TestCase;

final class LastTransactionDateTest extends TestCase
{
    public function testValidTime(): void
    {
        $date = new \DateTimeImmutable('2020-01-01');
        $time = new \DateTimeImmutable('23:59');

        $result = new LastTransactionDate($date, $time);

        $this->assertEquals($date, $result->getDate());
        $this->assertEquals($time, $result->getTime());
    }
}
