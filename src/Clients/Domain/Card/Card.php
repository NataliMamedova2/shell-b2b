<?php

namespace App\Clients\Domain\Card;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\ExportStatus;
use App\Clients\Domain\Card\ValueObject\CardId;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\CarNumber;
use App\Clients\Domain\Card\ValueObject\DayLimit;
use App\Clients\Domain\Card\ValueObject\MoneyLimits;
use App\Clients\Domain\Card\ValueObject\MonthLimit;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Card\ValueObject\TimeUse;
use App\Clients\Domain\Card\ValueObject\WeekLimit;
use App\Clients\Domain\Driver\Driver;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\Exception\DomainException;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cards",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"id", "card_number"})},
 *      indexes={@ORM\Index(columns={"card_number"})}
 * )
 */
class Card
{
    /**
     * @var string
     *
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(name="card_number", type="string", unique=true, nullable=false)
     */
    private $cardNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="client_1c_id", type="string", unique=false, nullable=false)
     */
    private $client1CId;

    /**
     * @var string
     *
     * @ORM\Column(name="car_number", type="string", nullable=true)
     */
    private $carNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Clients\Domain\Driver\Driver")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $driver;

    /**
     * @var int
     *
     * @ORM\Column(name="day_limit", type="bigint", nullable=false)
     */
    private $dayLimit;

    /**
     * @var int
     *
     * @ORM\Column(name="week_limit", type="bigint", nullable=false)
     */
    private $weekLimit;

    /**
     * @var int
     *
     * @ORM\Column(name="month_limit", type="bigint", nullable=false)
     */
    private $monthLimit;

    /**
     * @var string
     *
     * @ORM\Column(name="service_schedule", type="string", nullable=false)
     */
    private $serviceSchedule;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="time_use_from", type="datetime_immutable", nullable=false)
     */
    private $timeUseFrom;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="time_use_to", type="datetime_immutable", nullable=false)
     */
    private $timeUseTo;

    /**
     * @ORM\Column(name="status", type="smallint", nullable=false, options={"default": 0})
     */
    private $status;

    /**
     * @var ExportStatus
     * @ORM\Embedded(class="App\Application\Domain\ValueObject\ExportStatus", columnPrefix=false)
     */
    private $exportStatus;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $updatedAt;

    /**
     * @var ArrayCollection|StopList[]
     *
     * @ORM\OneToMany(targetEntity="App\Clients\Domain\Card\StopList", mappedBy="card", cascade={"persist"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $stopList;

    private function __construct(
        CardId $id,
        Client1CId $client1CId,
        CardNumber $cardNumber,
        CarNumber $carNumber,
        DayLimit $dayLimit,
        WeekLimit $weekLimit,
        MonthLimit $monthLimit,
        ServiceSchedule $serviceSchedule,
        TimeUse $timeUse,
        CardStatus $cardStatus
    ) {
        $this->id = $id;
        $this->client1CId = $client1CId;
        $this->cardNumber = $cardNumber;
        $this->carNumber = $carNumber;
        $this->dayLimit = $dayLimit->getValue();
        $this->weekLimit = $weekLimit->getValue();
        $this->monthLimit = $monthLimit->getValue();
        $this->serviceSchedule = $serviceSchedule->getValue();
        $this->timeUseFrom = $timeUse->getStartTime();
        $this->timeUseTo = $timeUse->getEndTime();
        $this->status = $cardStatus->getValue();
        $this->exportStatus = ExportStatus::new();

        $this->stopList = new ArrayCollection();
    }

    public static function create(
        CardId $id,
        Client1CId $client1CId,
        CardNumber $cardNumber,
        CarNumber $carNumber,
        DayLimit $dayLimit,
        WeekLimit $weekLimit,
        MonthLimit $monthLimit,
        ServiceSchedule $serviceSchedule,
        TimeUse $timeUse,
        CardStatus $cardStatus,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self(
            $id,
            $client1CId,
            $cardNumber,
            $carNumber,
            $dayLimit,
            $weekLimit,
            $monthLimit,
            $serviceSchedule,
            $timeUse,
            $cardStatus
        );

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public function update(
        Client1CId $client1CId,
        CarNumber $carNumber,
        DayLimit $dayLimit,
        WeekLimit $weekLimit,
        MonthLimit $monthLimit,
        ServiceSchedule $serviceSchedule,
        TimeUse $timeUse,
        CardStatus $cardStatus
    ): self {
        $this->client1CId = $client1CId;
        $this->carNumber = $carNumber;
        $this->dayLimit = $dayLimit->getValue();
        $this->weekLimit = $weekLimit->getValue();
        $this->monthLimit = $monthLimit->getValue();
        $this->serviceSchedule = $serviceSchedule->getValue();
        $this->timeUseFrom = $timeUse->getStartTime();
        $this->timeUseTo = $timeUse->getEndTime();
        $this->status = $cardStatus->getValue();

        $this->updatedAt = new \DateTimeImmutable();
        $this->exportStatus->applied();

        return $this;
    }

    public function change(MoneyLimits $moneyLimits, TimeUse $timeUse, ServiceSchedule $serviceSchedule): void
    {
        if (true === $this->isBlocked()) {
            throw new DomainException('You can\'t update blocked card.');
        }
        if (true === $this->getExportStatus()->onModeration()) {
            throw new DomainException('You can\'t update card on moderation.');
        }

        $currentMoneyLimits = new MoneyLimits($this->dayLimit / 100, $this->weekLimit / 100, $this->monthLimit / 100);
        $currentTimeUse = new TimeUse($this->timeUseFrom, $this->timeUseTo);
        $currentServiceSchedule = new ServiceSchedule($this->serviceSchedule);
        if (false === $currentMoneyLimits->equals($moneyLimits)
            || false === $currentTimeUse->equals($timeUse)
            || false === $currentServiceSchedule->equals($serviceSchedule)
        ) {
            $this->dayLimit = $moneyLimits->getDayLimit();
            $this->weekLimit = $moneyLimits->getWeekLimit();
            $this->monthLimit = $moneyLimits->getMonthLimit();
            $this->timeUseFrom = $timeUse->getStartTime();
            $this->timeUseTo = $timeUse->getEndTime();
            $this->serviceSchedule = $serviceSchedule->getValue();
            $this->updatedAt = new \DateTimeImmutable();

            $this->exportStatus->readyForExport();
        }
    }

    public function changeDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    public function deleteDriver(): void
    {
        $this->driver = null;
    }

    public function isBlocked(): bool
    {
        $status = new CardStatus($this->status);
        if (true === $status->isBlocked()) {
            return true;
        }

        return false;
    }

    public function getExportStatus(): ExportStatus
    {
        return $this->exportStatus;
    }

    public function block(): void
    {
        if (true === $this->isBlocked()) {
            throw new DomainException('Card already blocked.');
        }
        if (true === $this->getExportStatus()->onModeration()) {
            throw new DomainException('You can\'t block card on moderation.');
        }

        $this->status = CardStatus::blocked()->getValue();
        if (false === $this->cardInStopList()) {
            $this->stopList->add(new StopList($this, new \DateTimeImmutable()));
        }
    }

    public function cardInStopList(): bool
    {
        foreach ($this->stopList as $stopList) {
            if (true === $stopList->getExportStatus()->onModerationStopList()) {
                return true;
            }
        }

        return false;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getCarNumber(): string
    {
        return $this->carNumber;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }

    public function getDayLimit(): int
    {
        return $this->dayLimit;
    }

    public function getWeekLimit(): int
    {
        return $this->weekLimit;
    }

    public function getMonthLimit(): int
    {
        return $this->monthLimit;
    }

    public function getServiceSchedule(): string
    {
        return $this->serviceSchedule;
    }

    public function getTimeUseFrom(): \DateTimeInterface
    {
        return $this->timeUseFrom;
    }

    public function getTimeUseTo(): \DateTimeInterface
    {
        return $this->timeUseTo;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }
}
