<?php

namespace App\Clients\Domain\Invoice\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
final class Date
{
    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="date_immutable", nullable=false)
     */
    private $creationDate;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="date_immutable", nullable=false)
     */
    private $expirationDate;

    public function __construct(\DateTimeImmutable $dateTime, int $validDays)
    {
        Assert::greaterThan($validDays, 1);

        $this->creationDate = $dateTime;
        $this->expirationDate = $dateTime->modify(sprintf('+ %d days', $validDays));
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreationDate(): \DateTimeImmutable
    {
        return $this->creationDate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpirationDate(): \DateTimeImmutable
    {
        return $this->expirationDate;
    }
}
