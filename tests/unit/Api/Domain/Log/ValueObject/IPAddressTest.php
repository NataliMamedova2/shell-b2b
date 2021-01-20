<?php

namespace Tests\Unit\Api\Domain\Log\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Api\Domain\Log\ValueObject\IPAddress;

class IPAddressTest extends TestCase
{
    public function testCreateReturnObject(): void
    {
        $value = '127.0.0.1';
        $result = new IPAddress($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, (string) $result);
    }
}
