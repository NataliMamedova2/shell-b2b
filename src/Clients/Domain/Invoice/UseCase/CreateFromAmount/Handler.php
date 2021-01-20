<?php

namespace App\Clients\Domain\Invoice\UseCase\CreateFromAmount;

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
use Doctrine\Common\Persistence\ObjectManager;
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
    /**
     * @var Repository
     */
    private $fuelPriceRepository;

    public function __construct(
        Repository $repository,
        NumberGenerator $numberGenerator,
        InvoiceSettings $invoiceSettings,
        InvoiceFileService $invoiceFileService,
        Repository $fuelPriceRepository,
        Repository $documentRepository,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->numberGenerator = $numberGenerator;
        $this->invoiceSettings = $invoiceSettings;
        $this->invoiceFileService = $invoiceFileService;
        $this->fuelPriceRepository = $fuelPriceRepository;
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

        /** @var Price[] $prices */
        $prices = $this->fuelPriceRepository->findMany([
            'fuelCode_in' => ['КВЦ0000006', 'КВЦ0000008'],
        ]);

        $divider = count($prices);
        foreach ($prices as $key => $price) {
            $quantity = ($handlerRequest->amount / $divider) / ($price->getPriceWithTax() / 100);
            $entity->addItem(
                new LineNumber(++$key),
                new FuelCode($price->getFuelCode()),
                new ItemPrice($price->getPriceWithTax() / 100),
                new Quantity($quantity),
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
