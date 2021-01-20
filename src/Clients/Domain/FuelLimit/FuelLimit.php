<?php

namespace App\Clients\Domain\FuelLimit;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Domain\ValueObject\FuelCode;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\FuelLimit\ValueObject\DayLimit;
use App\Clients\Domain\FuelLimit\ValueObject\FuelId;
use App\Clients\Domain\FuelLimit\ValueObject\Limits;
use App\Clients\Domain\FuelLimit\ValueObject\MonthLimit;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Domain\FuelLimit\ValueObject\WeekLimit;
use Doctrine\ORM\Mapping as ORM;
use Domain\Exception\DomainException;

/**
 * @ORM\Entity()
 * @ORM\Table(name="fuel_limits",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"card_number", "fuel_code"})}
 * )
 */
class FuelLimit
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="client_1c_id", type="string", nullable=false)
     */
    private $client1CId;

    /**
     * @var string
     *
     * @ORM\Column(name="card_number", type="string", nullable=false)
     */
    private $cardNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="fuel_code", type="string", nullable=false)
     */
    private $fuelCode;

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
     * @ORM\Column(name="status", type="smallint", nullable=false, options={"default" : 0})
     */
    private $purseActivity;

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

    private function __construct(
        FuelId $id,
        Client1CId $client1CId,
        CardNumber $cardNumber,
        FuelCode $fuelCode,
        DayLimit $dayLimit,
        WeekLimit $weekLimit,
        MonthLimit $monthLimit,
        PurseActivity $purseActivity
    ) {
        $this->id = $id;
        $this->client1CId = $client1CId;
        $this->cardNumber = $cardNumber->getValue();
        $this->fuelCode = $fuelCode->getValue();
        $this->dayLimit = $dayLimit->getValue();
        $this->weekLimit = $weekLimit->getValue();
        $this->monthLimit = $monthLimit->getValue();
        $this->purseActivity = $purseActivity->getValue();

        $this->exportStatus = ExportStatus::new();
    }

    public static function create(
        FuelId $id,
        Client1CId $client1CId,
        CardNumber $cardNumber,
        FuelCode $fuelCode,
        DayLimit $dayLimit,
        WeekLimit $weekLimit,
        MonthLimit $monthLimit,
        PurseActivity $purseActivity,
        \DateTimeInterface $createdAt
    ): self {
        $self = new self($id, $client1CId, $cardNumber, $fuelCode, $dayLimit, $weekLimit, $monthLimit, $purseActivity);

        $self->createdAt = $createdAt;
        $self->updatedAt = $createdAt;

        return $self;
    }

    public static function createFromForm(Card $card, Type $productType, Limits $limits, \DateTimeInterface $dateTime): self
    {
        if (true === $card->isBlocked()) {
            throw new DomainException('You can\'t update blocked card.');
        }
        if (false === $card->getExportStatus()->onModeration()) {
            $card->getExportStatus()->readyForExport();
        }

        $self = new self(
            FuelId::next(),
            new Client1CId($card->getClient1CId()),
            new CardNumber($card->getCardNumber()),
            new FuelCode($productType->getFuelCode()),
            new DayLimit($limits->getDayLimit()),
            new WeekLimit($limits->getWeekLimit()),
            new MonthLimit($limits->getMonthLimit()),
            PurseActivity::active()
        );

        $self->createdAt = $dateTime;
        $self->updatedAt = $dateTime;
        $self->exportStatus->readyForExport();

        return $self;
    }

    public function update(
        Client1CId $client1CId,
        FuelCode $fuelCode,
        DayLimit $dayLimit,
        WeekLimit $weekLimit,
        MonthLimit $monthLimit,
        PurseActivity $purseActivity
    ): self {
        $this->client1CId = $client1CId;
        $this->fuelCode = $fuelCode;
        $this->dayLimit = $dayLimit->getValue();
        $this->weekLimit = $weekLimit->getValue();
        $this->monthLimit = $monthLimit->getValue();
        $this->purseActivity = $purseActivity->getValue();

        $this->updatedAt = new \DateTimeImmutable();
        $this->exportStatus->applied();

        return $this;
    }

    public function change(Card $card, Type $productType, Limits $limits, \DateTimeInterface $dateTime): void
    {
        if (true === $card->isBlocked()) {
            throw new DomainException('You can\'t update blocked card.');
        }
        if (false === $card->getExportStatus()->onModeration()) {
            $card->getExportStatus()->readyForExport();
        }

        $currentLimits = new Limits($this->dayLimit / 100, $this->weekLimit / 100, $this->monthLimit / 100);

        if (false === $currentLimits->equals($limits)
            || ($this->fuelCode !== $productType->getFuelCode())
        ) {
            $this->fuelCode = $productType->getFuelCode();
            $this->dayLimit = $limits->getDayLimit();
            $this->weekLimit = $limits->getWeekLimit();
            $this->monthLimit = $limits->getMonthLimit();
            $this->updatedAt = $dateTime;
            $this->purseActivity = PurseActivity::active()->getValue();

            $this->exportStatus->readyForExport();
        }
    }

    public function getExportStatus(): ExportStatus
    {
        return $this->exportStatus;
    }

    public function delete(Card $card, \DateTimeInterface $dateTime): void
    {
        if (true === $card->isBlocked()) {
            throw new DomainException('You can\'t update blocked card.');
        }
        if (false === $card->getExportStatus()->onModeration()) {
            $card->getExportStatus()->readyForExport();
        }

        $this->purseActivity = PurseActivity::markToRemove()->getValue();
        $this->updatedAt = $dateTime;

        $this->exportStatus->readyForExport();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getFuelCode(): string
    {
        return $this->fuelCode;
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

    public function getPurseActivity(): int
    {
        return $this->purseActivity;
    }
}
