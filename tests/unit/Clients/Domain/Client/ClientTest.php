<?php

namespace Tests\Unit\Clients\Domain\Client;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\Agent1CId;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Client\ValueObject\ClientId;
use App\Clients\Domain\Client\ValueObject\ContractNumber;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Client\ValueObject\EdrpouInn;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Client\ValueObject\NktId;
use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ClientId::fromString($string);

        $clientId = new Client1CId('КВ-0004888');
        $fullName = new FullName('Лавров Олександр Генадійович');
        $edrpouInn = new EdrpouInn('24584810');
        $type = new Type(0);
        $nktId = new NktId(9180004888);
        $managerId = new Manager1CId('КВЦ0000161');
        $agentId = new Agent1CId('');
        $fcCbrId = new FcCbrId(12435);
        $status = new Status(1);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');
        $entity = Client::create(
            $identity,
            $clientId,
            $fullName,
            $edrpouInn,
            $type,
            $nktId,
            $managerId,
            $agentId,
            $fcCbrId,
            $status,
            $dateTime
        );

        $this->assertEquals($clientId, $entity->getClient1CId());
        $this->assertEquals($fullName, $entity->getFullName());
        $this->assertEquals($type->getValue(), $entity->getType());
        $this->assertEquals($status->getValue(), $entity->getStatus());
        $this->assertEquals($managerId, $entity->getManager1CId());
        $this->assertEquals($dateTime, $entity->getUpdatedAt());
        $this->assertEquals(true, method_exists($entity, 'getCompany'));
        $this->assertEquals(true, method_exists($entity, 'getRegisterToken'));
    }

    public function testUpdate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ClientId::fromString($string);

        $clientId = new Client1CId('КВ-0004888');
        $fullName = new FullName('Лавров Олександр Генадійович');
        $edrpouInn = new EdrpouInn('24584810');
        $type = new Type(2);
        $nktId = new NktId(9180004888);
        $managerId = new Manager1CId('КВЦ0000161');
        $agentId = new Agent1CId('');
        $fcCbrId = new FcCbrId(12435);
        $status = new Status(1);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');
        $entity = Client::create(
            $identity,
            $clientId,
            $fullName,
            $edrpouInn,
            $type,
            $nktId,
            $managerId,
            $agentId,
            $fcCbrId,
            $status,
            $dateTime
        );

        $newFullName = 'new Title';
        $newEdrpouInn = '24584810';
        $newType = 1;
        $newNktId = 123456789;
        $newManager1CId = 'SDE0000001';
        $newAgent1CId = 'АКЦ0000001';
        $newFcCbrId = 12440;
        $newStatus = 0;

        $entity->update(
            new FullName($newFullName),
            new EdrpouInn($newAgent1CId),
            new Type($newType),
            new NktId($newNktId),
            new Manager1CId($newManager1CId),
            new Agent1CId($newAgent1CId),
            new FcCbrId($newFcCbrId),
            new Status($newStatus)
        );

        $this->assertEquals($clientId, $entity->getClient1CId());
        $this->assertEquals($newFullName, $entity->getFullName());
        $this->assertEquals($newType, $entity->getType());
        $this->assertEquals($newStatus, $entity->getStatus());
        $this->assertEquals($newManager1CId, $entity->getManager1CId());
        $this->assertNotEquals($dateTime, $entity->getUpdatedAt());
    }

    public static function createValidEntity(): Client
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ClientId::fromString($string);

        $clientId = new Client1CId('КВ-0001332');
        $fullName = new FullName('Лавров Олександр Генадійович');
        $edrpouInn = new EdrpouInn('24584810');
        $type = new Type(0);
        $nktId = new NktId(9180004888);
        $managerId = new Manager1CId('КВЦ0000161');
        $agentId = new Agent1CId('');
        $fcCbrId = new FcCbrId(12435);
        $status = new Status(1);
        $contractNumber = new ContractNumber('КРБК-010103');
        $contractDate = new \DateTimeImmutable('2019-01-01 00:00:00');

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Client::createWithContract(
            $identity,
            $clientId,
            $fullName,
            $edrpouInn,
            $type,
            $nktId,
            $managerId,
            $agentId,
            $fcCbrId,
            $status,
            $contractNumber,
            $contractDate,
            $dateTime
        );
    }
}
