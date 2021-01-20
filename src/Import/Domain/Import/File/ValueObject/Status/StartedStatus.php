<?php

declare(strict_types=1);

namespace App\Import\Domain\Import\File\ValueObject\Status;

final class StartedStatus extends Status
{
    protected $next = [CopiedStatus::class, FailedStatus::class];

    public function getValue(): string
    {
        return Status::STARTED;
    }
}
