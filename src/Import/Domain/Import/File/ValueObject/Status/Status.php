<?php

namespace App\Import\Domain\Import\File\ValueObject\Status;

use App\Import\Domain\Import\ValueObject\Exception\WrongStatusChangeDirectionException;

abstract class Status
{
    protected const STARTED = 'started';
    protected const COPIED = 'copied';
    protected const IN_PROGRESS = 'in-progress';
    protected const DONE = 'done';
    protected const ERROR = 'error';
    protected const FAILED = 'failed';

    /**
     * Class names of next possible statuses.
     *
     * @var array
     */
    protected $next = [];

    public function canBeChangedTo(self $status): bool
    {
        $className = get_class($status);

        return in_array($className, $this->next, true);
    }

    public function ensureCanBeChangedTo(self $status): void
    {
        if (!$this->canBeChangedTo($status)) {
            throw new WrongStatusChangeDirectionException();
        }
    }

    abstract public function getValue(): string;

    public static function getNames(): array
    {
        return [
            self::STARTED,
            self::COPIED,
            self::IN_PROGRESS,
            self::DONE,
            self::ERROR,
            self::FAILED,
        ];
    }
}
