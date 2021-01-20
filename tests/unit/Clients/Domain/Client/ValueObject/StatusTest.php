<?php

namespace Tests\Unit\Clients\Domain\Client\ValueObject;

use App\Clients\Domain\Client\ValueObject\Status;
use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    public function testCreateNotValidValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 3;
        new Status($value);
    }

    public function provider()
    {
        return [
            'in-work' => [0],
            'in-blacklist' => [1],
        ];
    }

    /**
     * @param $value
     * @dataProvider provider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Status($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }
}
