<?php

namespace Tests\Unit\Application\Domain\ValueObject;

use App\Application\Domain\ValueObject\ExportStatus;
use PHPUnit\Framework\TestCase;

final class ExportStatusTest extends TestCase
{
    public function testNewReturnObject(): void
    {
        $newValue = 0;
        $result = ExportStatus::new();

        $this->assertEquals($newValue, $result->getStatus());

        $readyForExportValue = 1;
        $result->readyForExport();
        $this->assertEquals($readyForExportValue, $result->getStatus());
    }
}
