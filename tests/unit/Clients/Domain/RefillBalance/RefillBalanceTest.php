<?php

namespace Tests\Unit\Clients\Domain\RefillBalance;

use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Domain\RefillBalance\ValueObject\Amount;
use App\Clients\Domain\RefillBalance\ValueObject\CardOwner;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\RefillBalance\ValueObject\Operation;
use App\Clients\Domain\RefillBalance\ValueObject\OperationDate;
use App\Clients\Domain\RefillBalance\ValueObject\RefillBalanceId;
use PHPUnit\Framework\TestCase;

final class RefillBalanceTest extends TestCase
{

    public function testConstructorReturnObject(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = RefillBalanceId::fromString($string);

        $cardOwner = new CardOwner(2);
        $fcCbrId = new FcCbrId(9180004888);
        $operation = new Operation(0);
        $amount = new Amount(0);
        $operationDate = \DateTimeImmutable::createFromFormat('d/m/y', '10/10/19');
        $operationTime = new \DateTimeImmutable('17:53:16');
        $dateTime = new OperationDate($operationDate, $operationTime);

        $entity = new RefillBalance(
            $identity,
            $cardOwner,
            $fcCbrId,
            $operation,
            $amount,
            $dateTime
        );

        $this->assertEquals($identity, $entity->getId());
        $this->assertEquals($fcCbrId, $entity->getFcCbrId());
        $this->assertEquals($operation->getSign(), $entity->getOperationSign());
        $this->assertEquals($amount->getValue(), $entity->getAmount());
        $this->assertEquals($dateTime->getValue(), $entity->getOperationDateTime());
    }

    public function testUpdate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = RefillBalanceId::fromString($string);

        $cardOwner = new CardOwner(2);
        $fcCbrId = new FcCbrId(9180004888);
        $operation = new Operation(0);
        $amount = new Amount(0);
        $operationDate = \DateTimeImmutable::createFromFormat('d/m/y', '10/10/19');
        $operationTime = new \DateTimeImmutable('17:53:16');
        $dateTime = new OperationDate($operationDate, $operationTime);

        $entity = new RefillBalance(
            $identity,
            $cardOwner,
            $fcCbrId,
            $operation,
            $amount,
            $dateTime
        );

        $newCardOwner = new CardOwner(3);
        $newFcCbrId = new FcCbrId('9180001322');
        $newOperation = new Operation(1);
        $newAmount = new Amount('00000001500000');
        $operationDate = \DateTimeImmutable::createFromFormat('d/m/y', '24/06/19');
        $operationTime = new \DateTimeImmutable('17:53:16');
        $newDateTime = new OperationDate($operationDate, $operationTime);

        $entity->update(
            $newCardOwner,
            $newFcCbrId,
            $newOperation,
            $newAmount,
            $newDateTime
        );

        $this->assertEquals($identity, $entity->getId());
        $this->assertEquals($newFcCbrId, $entity->getFcCbrId());
        $this->assertEquals($newOperation->getSign(), $entity->getOperationSign());
        $this->assertEquals($newAmount->getValue(), $entity->getAmount());
        $this->assertEquals($newDateTime->getValue(), $entity->getOperationDateTime());
    }

    public static function createValidEntity(): RefillBalance
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = RefillBalanceId::fromString($string);

        $cardOwner = new CardOwner(2);
        $fcCbrId = new FcCbrId(9180004888);
        $operation = new Operation(0);
        $amount = new Amount(0);
        $operationDate = \DateTimeImmutable::createFromFormat('d/m/y', '10/10/19');
        $operationTime = new \DateTimeImmutable('17:53:16');
        $dateTime = new OperationDate($operationDate, $operationTime);

        return new RefillBalance(
            $identity,
            $cardOwner,
            $fcCbrId,
            $operation,
            $amount,
            $dateTime
        );
    }
}
