<?php

namespace Tests\Unit\Clients\Domain\Document\ValueObject;

use App\Clients\Domain\Document\ValueObject\Status;
use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    /**
     * @param $value
     * @param $name
     *
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value, $name): void
    {
        $result = new Status($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider()
    {
        return [
            'formed-automatically' => [0, 'formed-automatically'],
            'formed-by-request' => [1, 'formed-by-request'],
        ];
    }

    public function testFormedByRequestReturnObject(): void
    {
        $formedByRequestValue = 1;
        $result = Status::formedByRequest();

        $this->assertEquals($formedByRequestValue, $result->getValue());

        $formedByRequestName = 'formed-by-request';
        $this->assertEquals($formedByRequestName, $result->getName());
        $this->assertEquals($formedByRequestValue, $result->__toString());
        $this->assertEquals($formedByRequestValue, (string) $result);
    }

    /**
     * @param $value
     * @dataProvider invalidDataProvider
     */
    public function testCreateInvalidValueReturnObject($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Status($value);
    }

    public function invalidDataProvider()
    {
        return [
            'negative' => [-1],
            'more than max' => [3],
        ];
    }
}
