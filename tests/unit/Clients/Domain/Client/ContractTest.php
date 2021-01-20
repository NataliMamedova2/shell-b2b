<?php

namespace Tests\Unit\Clients\Domain\Client;

use App\Clients\Domain\Client\Contract;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Client\ValueObject\ContractId;
use App\Clients\Domain\Client\ValueObject\DsgCaGhb;
use App\Clients\Domain\Client\ValueObject\EckDsgCa;
use App\Clients\Domain\Client\ValueObject\FixedSum;
use PHPUnit\Framework\TestCase;

final class ContractTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ContractId::fromString($string);

        $clientId = new Client1CId('КВ-0004888');
        $eckDsgCa = new EckDsgCa(1);
        $dsgCaGhb = new DsgCaGhb(0);
        $fixedSum = new FixedSum(0);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');
        $entity = Contract::create(
            $identity,
            $clientId,
            $eckDsgCa,
            $dsgCaGhb,
            $fixedSum,
            $dateTime
        );

        $this->assertEquals($clientId, $entity->getClient1CId());
    }

    public function testUpdate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ContractId::fromString($string);

        $clientId = new Client1CId('КВ-0004888');
        $eckDsgCa = new EckDsgCa(1);
        $dsgCaGhb = new DsgCaGhb(0);
        $fixedSum = new FixedSum(0);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');
        $entity = Contract::create(
            $identity,
            $clientId,
            $eckDsgCa,
            $dsgCaGhb,
            $fixedSum,
            $dateTime
        );

        $newEckDsgCa = 2;
        $newDsgCaGhb = 123456789;
        $newFixedSum = 999456789;

        $entity->update(
            new EckDsgCa($newEckDsgCa),
            new DsgCaGhb($newDsgCaGhb),
            new FixedSum($newFixedSum)
        );

        $this->assertEquals($clientId, $entity->getClient1CId());
    }
}
