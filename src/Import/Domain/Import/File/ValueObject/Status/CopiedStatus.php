<?php

declare(strict_types=1);

namespace App\Import\Domain\Import\File\ValueObject\Status;

final class CopiedStatus extends Status
{
    protected $next = [InProgressStatus::class, ErrorStatus::class, FailedStatus::class];

    public function getValue(): string
    {
        return Status::COPIED;
    }
}
