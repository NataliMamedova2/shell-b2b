<?php

namespace Tests\Unit\Clients\Domain\RefillBalance\ValueObject;

use App\Clients\Domain\RefillBalance\ValueObject\Operation;
use PHPUnit\Framework\TestCase;

final class OperationTest extends TestCase
{
    public function testCreateWriteOffOperation(): void
    {
        $writeOffValue = 0;
        $result = new Operation($writeOffValue);

        $this->assertEquals($writeOffValue, $result->getValue());
        $this->assertEquals($writeOffValue, $result->__toString());
        $writeOffSign = '-';
        $this->assertEquals($writeOffSign, $result->getSign());
    }

    public function testCreateRefillOperation(): void
    {
        $writeOffValue = 1;
        $result = new Operation($writeOffValue);

        $this->assertEquals($writeOffValue, $result->getValue());
        $this->assertEquals($writeOffValue, $result->__toString());
        $writeOffSign = '+';
        $this->assertEquals($writeOffSign, $result->getSign());
    }
}
