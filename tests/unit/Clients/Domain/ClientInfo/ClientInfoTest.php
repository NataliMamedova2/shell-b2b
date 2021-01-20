<?php

namespace Tests\Unit\Clients\Domain\ClientInfo;

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\ClientInfo\ValueObject\Balance;
use App\Clients\Domain\ClientInfo\ValueObject\ClientPcId;
use App\Clients\Domain\ClientInfo\ValueObject\CreditLimit;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\ClientInfo\ValueObject\LastTransactionDate;
use PHPUnit\Framework\TestCase;

final class ClientInfoTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $clientPcId = new ClientPcId('001110000001');
        $fcCbrId = new FcCbrId('000000001234');

        $balanceValue = '20';
        $balance = new Balance($balanceValue);
        $lastTransactionDate = new LastTransactionDate(
            \DateTimeImmutable::createFromFormat('d/m/Y', '01/01/1900'),
            new \DateTimeImmutable('00:00:00')
        );
        $creditLimitValue = 100000;
        $creditLimit = new CreditLimit($creditLimitValue);
        $dateTimeMock = $this->prophesize(\DateTimeImmutable::class);

        $entity = ClientInfo::create(
            $identity,
            $clientPcId,
            $fcCbrId,
            $balance,
            $creditLimit,
            $lastTransactionDate,
            $dateTimeMock->reveal()
        );

        $this->assertEquals($balanceValue * 100, $entity->getBalance());
        $this->assertEquals($creditLimitValue * 100, $entity->getCreditLimit());
        $this->assertEquals($clientPcId->getValue(), (string) $entity->getClientPcId());
    }

    public function testUpdate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $clientPcId = new ClientPcId('001110000001');
        $fcCbrId = new FcCbrId('000000001234');

        $balanceValue = '20';
        $balance = new Balance($balanceValue);
        $lastTransactionDate = new LastTransactionDate(
            \DateTimeImmutable::createFromFormat('d/m/Y', '01/01/1900'),
            new \DateTimeImmutable('00:00:00')
        );
        $creditLimitValue = 100000;
        $creditLimit = new CreditLimit($creditLimitValue);
        $dateTime = new \DateTimeImmutable();

        $entity = ClientInfo::create(
            $identity,
            $clientPcId,
            $fcCbrId,
            $balance,
            $creditLimit,
            $lastTransactionDate,
            $dateTime
        );

        $newBalanceValue = -44.21;
        $newCreditLimitValue = 300000;

        $newLastTransactionDate = new LastTransactionDate(
            \DateTimeImmutable::createFromFormat('d/m/Y', '01/01/2020'),
            new \DateTimeImmutable('12:21:21')
        );

        $dateTime = new \DateTimeImmutable();
        $entity->update(
            new Balance($newBalanceValue),
            new CreditLimit($newCreditLimitValue),
            $newLastTransactionDate,
            $dateTime
        );

        $this->assertEquals($newBalanceValue * 100, $entity->getBalance());
        $this->assertEquals($newCreditLimitValue * 100, $entity->getCreditLimit());
        $this->assertEquals($clientPcId->getValue(), (string) $entity->getClientPcId());
    }

    public static function createValidEntity(array $data = []): ClientInfo
    {
        $balance = 20;
        $default = [
            'balance' => $balance,
        ];

        $data = array_merge($default, $data);

        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $clientPCId = new ClientPcId('001110000001');
        $fcCbrId = new FcCbrId('000000001234');
        $balance = new Balance($data['balance']);
        $lastTransactionDate = new LastTransactionDate(
            \DateTimeImmutable::createFromFormat('d/m/Y', '01/01/1900'),
            new \DateTimeImmutable('00:00:00')
        );
        $creditLimit = new CreditLimit('100000');
        $dateTime = new \DateTimeImmutable();

        return ClientInfo::create(
            $identity,
            $clientPCId,
            $fcCbrId,
            $balance,
            $creditLimit,
            $lastTransactionDate,
            $dateTime
        );
    }
}
