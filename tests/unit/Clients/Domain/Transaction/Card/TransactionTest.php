<?php

namespace Tests\Unit\Clients\Domain\Transaction\Card;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Transaction\Card\Transaction;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcCode;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcName;
use App\Clients\Domain\Transaction\Card\ValueObject\Debit;
use App\Clients\Domain\Transaction\Card\ValueObject\FuelQuantity;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionCode;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionName;
use App\Clients\Domain\Transaction\Card\ValueObject\StellaPrice;
use App\Clients\Domain\Transaction\Card\ValueObject\Transaction1CId;
use App\Clients\Domain\Transaction\Card\ValueObject\TransactionId;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = TransactionId::fromString($string);

        $transaction1CId = new Transaction1CId('DF4D106456E01CA18443001BBFA6013A');
        $clientId = new Client1CId('КВЦ0007669');
        $cardNumber = new CardNumber('2101031263');
        $fuelCode = new FuelCode('КВЦ0000008');

        $fuelQuantityValue = 10000;
        $fuelQuantity = new FuelQuantity($fuelQuantityValue);

        $stellaPriceValue = 2929;
        $stellaPrice = new StellaPrice($stellaPriceValue);

        $debitValue = 292900;
        $debit = new Debit($debitValue);
        $azsCode = new AzcCode('КВЦ0000139');
        $azsName = new AzcName('АЗС №R1106 Запорізька обл., м.Василівка, віул. Леніна, 2А');
        $regionCode = new RegionCode('КВЦ0000046');
        $regionName = new RegionName('Запоріжжя АХУ');
        $postDate = new \DateTimeImmutable('2019-03-01 00:09:52');

        $writeOffTypeValue = 1;
        $type = new Type($writeOffTypeValue);
        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        $entity = Transaction::create(
            $identity,
            $transaction1CId,
            $clientId,
            $cardNumber,
            $fuelCode,
            $fuelQuantity,
            $stellaPrice,
            $debit,
            $azsCode,
            $azsName,
            $regionCode,
            $regionName,
            $postDate,
            $type,
            $dateTime
        );

        $this->assertEquals($string, $entity->getId());
        $this->assertEquals((string) $clientId, $entity->getClient1CId());
        $this->assertEquals($fuelCode, $entity->getFuelCode());
        $this->assertEquals($transaction1CId, $entity->getTransaction1CId());
        $this->assertEquals($fuelQuantityValue, $entity->getFuelQuantity());
        $this->assertEquals($cardNumber, $entity->getCardNumber());
        $this->assertEquals($azsName, $entity->getAzsName());
        $this->assertEquals($debitValue, $entity->getDebit());
        $this->assertEquals($stellaPriceValue, $entity->getPrice());
        $this->assertEquals($postDate, $entity->getPostDate());
    }

    public static function createValidEntity(array $data = []): Transaction
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = TransactionId::fromString($string);

        $typeWriteOffValue = 1;
        $default = [
            'type' => $typeWriteOffValue,
            'fuelCode' => 'КВЦ0000008',
        ];

        $data = array_merge($default, $data);

        $transaction1CId = new Transaction1CId('DF4D106456E01CA18443001BBFA6013A');
        $clientId = new Client1CId('КВЦ0007669');
        $cardNumber = new CardNumber('2101031263');
        $fuelCode = new FuelCode($data['fuelCode']);

        $fuelQuantityValue = 10000;
        $fuelQuantity = new FuelQuantity($fuelQuantityValue);

        $stellaPriceValue = 2929;
        $stellaPrice = new StellaPrice($stellaPriceValue);

        $debitValue = 292900;
        $debit = new Debit($debitValue);
        $azsCode = new AzcCode('КВЦ0000139');
        $azsName = new AzcName('АЗС №R1106 Запорізька обл., м.Василівка, віул. Леніна, 2А');
        $regionCode = new RegionCode('КВЦ0000046');
        $regionName = new RegionName('Запоріжжя АХУ');
        $postDate = new \DateTimeImmutable('2019-03-01 00:09:52');

        $type = new Type($data['type']);
        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Transaction::create(
            $identity,
            $transaction1CId,
            $clientId,
            $cardNumber,
            $fuelCode,
            $fuelQuantity,
            $stellaPrice,
            $debit,
            $azsCode,
            $azsName,
            $regionCode,
            $regionName,
            $postDate,
            $type,
            $dateTime
        );
    }
}
