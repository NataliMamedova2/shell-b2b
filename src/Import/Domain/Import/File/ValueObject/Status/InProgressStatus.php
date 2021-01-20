<?php

declare(strict_types=1);

namespace App\Import\Domain\Import\File\ValueObject\Status;

final class InProgressStatus extends Status
{
    protected $next = [DoneStatus::class, ErrorStatus::class, FailedStatus::class];

    public function getValue(): string
    {
        return Status::IN_PROGRESS;
    }
}
