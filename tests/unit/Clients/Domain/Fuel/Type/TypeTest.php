<?php

namespace Tests\Unit\Clients\Domain\Fuel\Type;

use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Fuel\Type\ValueObject\PurseCode;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelName;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelPurse;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Fuel\Type\ValueObject\TypeId;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = TypeId::fromString($string);

        $fuelCode = new FuelCode('КВ-00000001');
        $fuelName = new FuelName('Бензин Mustang');
        $fuelPurse = new FuelPurse(1);
        $fuelType = new FuelType(1);
        $additionalType = new PurseCode(2);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        $entity = Type::create(
            $identity,
            $fuelCode,
            $fuelName,
            $fuelPurse,
            $fuelType,
            $additionalType,
            $dateTime
        );

        $this->assertEquals((string) $fuelCode, (string) $entity->getFuelCode());
        $this->assertEquals((string) $fuelName, (string) $entity->getFuelName());
    }

    public static function createValidEntity(): Type
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = TypeId::fromString($string);

        $fuelCode = new FuelCode('КВ-00000001');
        $fuelName = new FuelName('Бензин Mustang');
        $fuelPurse = new FuelPurse(1);
        $fuelType = new FuelType(1);
        $additionalType = new PurseCode(2);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Type::create(
            $identity,
            $fuelCode,
            $fuelName,
            $fuelPurse,
            $fuelType,
            $additionalType,
            $dateTime
        );
    }
}
