<?php

namespace Tests\Unit\Clients\Domain\ClientInfo;

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\ClientInfo\BalanceHistory;
use App\Clients\Domain\ClientInfo\ClientInfo;
use Domain\Exception\DomainException;
use PHPUnit\Framework\TestCase;

final class BalanceHistoryTest extends TestCase
{
    public function testConstructNotFirstDayOfMonthReturnException(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $clientInfoMock = $this->prophesize(ClientInfo::class);
        $dateMock = $this->prophesize(\DateTimeImmutable::class);

        $dateMock->format('j')
            ->shouldBeCalled()
            ->willReturn('2');

        $this->expectException(DomainException::class);

        new BalanceHistory($identity, $clientInfoMock->reveal(), $dateMock->reveal());
    }

    public function testConstructFirstDayOfMonthCreateObject(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = IdentityId::fromString($string);

        $clientInfoMock = $this->prophesize(ClientInfo::class);
        $dateMock = $this->prophesize(\DateTimeImmutable::class);

        $dayValue = '1';
        $dateMock->format('j')
            ->shouldBeCalled()
            ->willReturn($dayValue);

        $balanceValue = 12344;
        $clientInfoMock->getBalance()
            ->shouldBeCalled()
            ->willReturn($balanceValue);

        $entity = new BalanceHistory($identity, $clientInfoMock->reveal(), $dateMock->reveal());

        $this->assertEquals($balanceValue, $entity->getBalance());
        $this->assertEquals($dayValue, $entity->getDate()->format('j'));
    }
}
