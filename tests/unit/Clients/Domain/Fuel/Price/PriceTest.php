<?php

namespace Tests\Unit\Clients\Domain\Fuel\Price;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Price\Price;
use App\Clients\Domain\Fuel\Price\ValueObject\FuelPrice;
use App\Clients\Domain\Fuel\Price\ValueObject\PriceId;
use PHPUnit\Framework\TestCase;

final class PriceTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = PriceId::fromString($string);

        $priceInCoin = 3200;
        $fuelCode = new FuelCode('КВ-00000001');
        $fuelPrice = new FuelPrice($priceInCoin);
        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        $entity = Price::create(
            $identity,
            $fuelCode,
            $fuelPrice,
            $dateTime
        );

        $this->assertEquals((string) $fuelCode, (string) $entity->getFuelCode());
        $this->assertEquals($priceInCoin, $entity->getPriceWithTax());
    }

    public static function createValidEntity(): Price
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = PriceId::fromString($string);

        $fuelCode = new FuelCode('КВ-00000001');
        $fuelPrice = new FuelPrice(2399);
        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Price::create(
            $identity,
            $fuelCode,
            $fuelPrice,
            $dateTime
        );
    }
}
