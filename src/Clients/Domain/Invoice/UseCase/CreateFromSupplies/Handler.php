<?php

namespace App\Clients\Domain\Invoice\UseCase\CreateFromSupplies;

use App\Application\Domain\ValueObject\FuelCode;
use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Document\Document;
use App\Clients\Domain\Document\Service\InvoiceFileService;
use App\Clients\Domain\Fuel\Price\Price;
use App\Clients\Domain\Invoice\Invoice;
use App\Clients\Domain\Invoice\Service\InvoiceSettings;
use App\Clients\Domain\Invoice\Service\NumberGenerator;
use App\Clients\Domain\Invoice\ValueObject\Date;
use App\Clients\Domain\Invoice\ValueObject\InvoiceNumber;
use App\Clients\Domain\Invoice\ValueObject\ItemPrice;
use App\Clients\Domain\Invoice\ValueObject\LineNumber;
use App\Clients\Domain\Invoice\ValueObject\Quantity;
use App\Clients\Domain\Invoice\ValueObject\ValueTax;
use App\Clients\Infrastructure\Fuel\Criteria\PriceByFuelId;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\InvalidArgumentException;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;

final class Handler implements DomainHandler
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var NumberGenerator
     */
    private $numberGenerator;
    /**
     * @var InvoiceSettings
     */
    private $invoiceSettings;
    /**
     * @var Repository
     */
    private $fuelRepository;
    /**
     * @var Repository
     */
    private $fuelPriceRepository;
    /**
     * @var InvoiceFileService
     */
    private $invoiceFileService;
    /**
     * @var Repository
     */
    private $documentRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Repository $repository,
        NumberGenerator $numberGenerator,
        InvoiceSettings $invoiceSettings,
        Repository $fuelRepository,
        Repository $fuelPriceRepository,
        InvoiceFileService $invoiceFileService,
        Repository $documentRepository,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->numberGenerator = $numberGenerator;
        $this->invoiceSettings = $invoiceSettings;
        $this->fuelRepository = $fuelRepository;
        $this->fuelPriceRepository = $fuelPriceRepository;
        $this->invoiceFileService = $invoiceFileService;
        $this->documentRepository = $documentRepository;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = Invoice::create(
            IdentityId::next(),
            $handlerRequest->client->getClient1CId(),
            new InvoiceNumber($this->numberGenerator->next()),
            new ValueTax($this->invoiceSettings->getValueAddedTax() / 100),
            new Date(new \DateTimeImmutable(), $this->invoiceSettings->getInvoiceValidDays())
        );

        foreach ($handlerRequest->items as $key => $item) {
            if (!isset($item['id']) || !isset($item['volume'])) {
                throw new InvalidArgumentException('Invalid item');
            }

            /** @var Price $price */
            $price = $this->fuelPriceRepository->find([
                PriceByFuelId::class => $item['id'],
            ]);

            $entity->addItem(
                new LineNumber(++$key),
                new FuelCode($price->getFuelCode()),
                new ItemPrice($price->getPriceWithTax() / 100),
                new Quantity($item['volume']),
                new \DateTimeImmutable()
            );
        }

        $file = $this->invoiceFileService->create($entity);
        $document = Document::createFromInvoice($entity, $file, new \DateTimeImmutable());

        $this->repository->add($entity);
        $this->documentRepository->add($document);

        $this->objectManager->flush();

        return $document;
    }
}
