<?php

namespace App\Clients\Domain\Card;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Domain\ValueObject\IdentityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cards_stop_list")
 */
class StopList
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
     * @ORM\Column(name="client_1c_id", type="string", unique=false, nullable=false)
     */
    private $client1CId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Clients\Domain\Card\Card", inversedBy="stopList", cascade={"persist"})
     * @ORM\JoinColumn(name="card_number", referencedColumnName="card_number", onDelete="CASCADE")
     */
    private $card;

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

    public function __construct(Card $card, \DateTimeInterface $dateTime)
    {
        if (false === $card->isBlocked()) {
            $card->block();
        }
        $this->card = $card;

        $this->id = IdentityId::next();
        $this->client1CId = $card->getClient1CId();
        $this->exportStatus = ExportStatus::new();
        $this->exportStatus->readyForExport();

        $this->createdAt = $dateTime;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient1CId(): string
    {
        return $this->client1CId;
    }

    /**
     * @return Card
     */
    public function getCard(): Card
    {
        return $this->card;
    }

    public function getExportStatus(): ExportStatus
    {
        return $this->exportStatus;
    }
}
