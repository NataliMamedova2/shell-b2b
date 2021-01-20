<?php

namespace Tests\Unit\Clients\Domain\Card;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardId;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\CarNumber;
use App\Clients\Domain\Card\ValueObject\DayLimit;
use App\Clients\Domain\Card\ValueObject\MoneyLimits;
use App\Clients\Domain\Card\ValueObject\MonthLimit;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Card\ValueObject\TimeUse;
use App\Clients\Domain\Card\ValueObject\WeekLimit;
use Domain\Exception\DomainException;
use PHPUnit\Framework\TestCase;

final class CardTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = CardId::fromString($string);

        $clientId = new Client1CId('KV-0000001');
        $cardNumber = new CardNumber('0001059000');
        $carNumber = new CarNumber('');
        $dayLimit = new DayLimit(10000000);
        $weekLimit = new WeekLimit(70000000);
        $monthLimit = new MonthLimit(300000000);
        $serviceSchedule = new ServiceSchedule(1111111);
        $timeUse = new TimeUse(new \DateTimeImmutable('00:00:00'), new \DateTimeImmutable('23:59:59'));
        $statusActive = 1;
        $cardStatus = new CardStatus($statusActive);
        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        $entity = Card::create(
            $identity,
            $clientId,
            $cardNumber,
            $carNumber,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $serviceSchedule,
            $timeUse,
            $cardStatus,
            $dateTime
        );

        $this->assertEquals((string) $cardNumber, $entity->getCardNumber());
        $this->assertEquals($timeUse->getStartTime(), $entity->getTimeUseFrom());
        $this->assertEquals($timeUse->getEndTime(), $entity->getTimeUseTo());
        $this->assertEquals($dayLimit->getValue(), $entity->getDayLimit());
        $this->assertEquals($weekLimit->getValue(), $entity->getWeekLimit());
        $this->assertEquals($monthLimit->getValue(), $entity->getMonthLimit());
        $this->assertEquals($serviceSchedule->getValue(), $entity->getServiceSchedule());
        $this->assertEquals($cardStatus->getValue(), $entity->getStatus());

        $this->assertEquals(false, $entity->getExportStatus()->onModeration());
        $this->assertEquals(0, $entity->getExportStatus()->getStatus());
        $this->assertEquals(false, $entity->isBlocked());
    }

    public function testChange(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = CardId::fromString($string);

        $clientId = new Client1CId('KV-0000001');
        $cardNumber = new CardNumber('0001059000');
        $carNumber = new CarNumber('');
        $dayLimit = new DayLimit(10000000);
        $weekLimit = new WeekLimit(70000000);
        $monthLimit = new MonthLimit(300000000);
        $serviceSchedule = new ServiceSchedule(1111111);
        $timeUse = new TimeUse(new \DateTimeImmutable('00:00:00'), new \DateTimeImmutable('23:59:59'));
        $statusActive = 1;
        $cardStatus = new CardStatus($statusActive);
        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        $card = Card::create(
            $identity,
            $clientId,
            $cardNumber,
            $carNumber,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $serviceSchedule,
            $timeUse,
            $cardStatus,
            $dateTime
        );

        $changeMoneyLimits = new MoneyLimits(20000000, 80000000, 400000000);
        $changeTimeUse = new TimeUse(new \DateTimeImmutable('00:21'), new \DateTimeImmutable('22:22'));
        $changeServiceSchedule = new ServiceSchedule('0000000');

        $card->change($changeMoneyLimits, $changeTimeUse, $changeServiceSchedule);

        $this->assertEquals(true, $card->getExportStatus()->onModeration());
        $this->assertEquals(false, $card->isBlocked());

        $this->assertEquals($cardStatus->getValue(), (string) $card->getStatus());

        $this->assertEquals($changeMoneyLimits->getDayLimit(), $card->getDayLimit());
        $this->assertEquals($changeMoneyLimits->getWeekLimit(), $card->getWeekLimit());
        $this->assertEquals($changeMoneyLimits->getMonthLimit(), $card->getMonthLimit());
        $this->assertEquals($changeTimeUse->getStartTime(), $card->getTimeUseFrom());
        $this->assertEquals($changeTimeUse->getEndTime(), $card->getTimeUseTo());
        $this->assertEquals($changeServiceSchedule->getValue(), $card->getServiceSchedule());
    }

    public function testChangeHaveNotAppliedChangeReturnException(): void
    {
        $statusActive = 1;
        $card = CardTest::createValidEntity(['status' => $statusActive]);

        $moneyLimits = new MoneyLimits(20000000, 80000000, 400000000);
        $timeUse = new TimeUse(new \DateTimeImmutable('00:21'), new \DateTimeImmutable('22:22'));
        $serviceSchedule = new ServiceSchedule('0000000');

        $card->change($moneyLimits, $timeUse, $serviceSchedule);

        $this->expectException(DomainException::class);
        $card->change($moneyLimits, $timeUse, $serviceSchedule);
    }

    public function testChangeCardIsBlockedReturnException(): void
    {
        $statusBlocked = 0;
        $card = self::createValidEntity(['status' => $statusBlocked]);
        $this->assertEquals(true, $card->isBlocked());

        $moneyLimits = new MoneyLimits(20000000, 80000000, 400000000);
        $timeUse = new TimeUse(new \DateTimeImmutable('00:21'), new \DateTimeImmutable('22:22'));
        $serviceSchedule = new ServiceSchedule('0000000');

        $this->expectException(DomainException::class);
        $card->change($moneyLimits, $timeUse, $serviceSchedule);
    }

    public function testBlockCardAlreadyBlockedReturnException(): void
    {
        $statusBlocked = 0;
        $card = self::createValidEntity(['status' => $statusBlocked]);

        $this->expectException(DomainException::class);
        $card->block();
    }

    public function testBlockCardInStopListReturnException(): void
    {
        $statusActive = 1;
        $card = CardTest::createValidEntity(['status' => $statusActive]);
        $card->block();

        $this->expectException(DomainException::class);
        $card->block();
    }

    public function testIsBlockedStatusIsBlockedReturnTrue(): void
    {
        $statusBlocked = 0;
        $entity = self::createValidEntity(['status' => $statusBlocked]);

        $this->assertEquals(true, $entity->isBlocked());
    }

    public function testIsBlockedCardInStopListReturnTrue(): void
    {
        $statusActive = 1;
        $entity = self::createValidEntity(['status' => $statusActive]);
        $entity->block();

        $this->assertEquals(true, $entity->isBlocked());
    }

    public function testIsBlockedActiveCardReturnFalse(): void
    {
        $statusActive = 1;
        $entity = self::createValidEntity(['status' => $statusActive]);

        $this->assertEquals(false, $entity->isBlocked());
    }

    public static function createValidEntity(array $data = []): Card
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = CardId::fromString($string);

        $statusActive = 1;
        $default = [
            'status' => $statusActive,
        ];

        $data = array_merge($default, $data);

        $clientId = new Client1CId('KV-0000001');
        $cardNumber = new CardNumber('0001059000');
        $carNumber = new CarNumber('');
        $dayLimit = new DayLimit(10000000);
        $weekLimit = new WeekLimit(70000000);
        $monthLimit = new MonthLimit(300000000);
        $serviceSchedule = new ServiceSchedule(1111111);
        $timeUse = new TimeUse(new \DateTimeImmutable('00:00:00'), new \DateTimeImmutable('23:59:59'));
        $cardStatus = new CardStatus($data['status']);
        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        return Card::create(
            $identity,
            $clientId,
            $cardNumber,
            $carNumber,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $serviceSchedule,
            $timeUse,
            $cardStatus,
            $dateTime
        );
    }
}
