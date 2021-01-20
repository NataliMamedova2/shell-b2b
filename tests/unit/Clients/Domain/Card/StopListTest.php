<?php

namespace Tests\Unit\Clients\Domain\Card;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Clients\Domain\Card\StopList;
use PHPUnit\Framework\TestCase;

final class StopListTest extends TestCase
{

    public function testCreateCardIsBlockedNotInStopList()
    {
        $card = CardTest::createValidEntity(['status' => 0]);

        $result = new StopList($card, new \DateTimeImmutable());

        $this->assertEquals(ExportStatus::readyForExportStatus(), $result->getExportStatus()->getStatus());
    }

    public function testCreateReturnStatusNew()
    {
        $card = CardTest::createValidEntity(['status' => 1]);

        $result = new StopList($card, new \DateTimeImmutable());

        $this->assertEquals(ExportStatus::readyForExportStatus(), $result->getExportStatus()->getStatus());
    }
}
