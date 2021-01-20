<?php

declare(strict_types=1);

namespace App\Application\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Domain\Exception\DomainException;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
final class ExportStatus
{
    private const NEW = 0;
    private const READY_FOR_EXPORT = 1;
    private const IN_PROGRESS = 2;
    private const EXPORTED = 3;
    private const APPLIED = 4;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"default": 0})
     */
    private $exportStatus;

    private function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::NEW,
            self::READY_FOR_EXPORT,
            self::IN_PROGRESS,
            self::EXPORTED,
            self::APPLIED,
        ]);

        $this->exportStatus = $value;
    }

    public function getStatus(): int
    {
        return $this->exportStatus;
    }

    public static function new(): self
    {
        return new self(self::NEW);
    }

    public function readyForExport(): void
    {
        if (false === in_array($this->exportStatus, self::canBeEditedStatuses())) {
            throw new DomainException('You can\'t change status');
        }
        $this->exportStatus = self::READY_FOR_EXPORT;
    }

    public function inProgress(): void
    {
        if (false === in_array($this->exportStatus, [self::READY_FOR_EXPORT])) {
            throw new DomainException('You can\'t change status');
        }
        $this->exportStatus = self::IN_PROGRESS;
    }

    public function exported(): void
    {
        if (false === in_array($this->exportStatus, [self::IN_PROGRESS])) {
            throw new DomainException('You can\'t change status');
        }
        $this->exportStatus = self::EXPORTED;
    }

    public function revert(): void
    {
        if (self::IN_PROGRESS !== $this->exportStatus) {
            throw new DomainException('You can\'t revert this record');
        }
        $this->exportStatus = self::READY_FOR_EXPORT;
    }

    public function applied(): void
    {
        if (self::EXPORTED === $this->exportStatus) {
            $this->exportStatus = self::APPLIED;
        }
    }

    public function onModeration(): bool
    {
        return in_array($this->exportStatus, [self::READY_FOR_EXPORT, self::IN_PROGRESS, self::EXPORTED]);
    }

    public function onModerationStopList(): bool
    {
        return in_array($this->exportStatus, [self::READY_FOR_EXPORT, self::IN_PROGRESS]);
    }

    public static function readyForExportStatus(): int
    {
        return self::READY_FOR_EXPORT;
    }

    public static function canBeEditedStatuses(): array
    {
        return [self::NEW, self::APPLIED];
    }

    public static function cantBeEditedStatuses(): array
    {
        return [self::READY_FOR_EXPORT, self::IN_PROGRESS, self::EXPORTED];
    }
}
