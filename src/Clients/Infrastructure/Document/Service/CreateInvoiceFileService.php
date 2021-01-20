<?php

namespace App\Clients\Infrastructure\Document\Service;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Document\Service\InvoiceFileService;
use App\Clients\Domain\Document\ValueObject\File;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Invoice\Invoice;
use App\Clients\Domain\ShellInformation\ShellInformation;
use App\Clients\Infrastructure\Fuel\Criteria\IndexByFuelCode;
use FilesUploader\File\PathGeneratorInterface;
use Infrastructure\Exception\InvalidArgumentException;
use Infrastructure\Interfaces\Repository\Repository;
use Knp\Snappy\Pdf;
use Twig\Environment as Templating;

final class CreateInvoiceFileService implements InvoiceFileService
{
    /**
     * @var Repository
     */
    private $shellInfoRepository;
    /**
     * @var Repository
     */
    private $clientRepository;
    /**
     * @var Repository
     */
    private $fuelRepository;
    /**
     * @var Pdf
     */
    private $snappyPdf;
    /**
     * @var Templating
     */
    private $templating;
    /**
     * @var PathGeneratorInterface
     */
    private $pathGenerator;
    /**
     * @var string
     */
    private $rootPath;
    /**
     * @var string
     */
    private $sourcePath;

    public function __construct(
        Repository $shellInfoRepository,
        Repository $clientRepository,
        Repository $fuelRepository,
        Pdf $snappyPdf,
        Templating $templating,
        PathGeneratorInterface $pathGenerator,
        string $rootPath,
        string $sourcePath
    ) {
        $this->shellInfoRepository = $shellInfoRepository;
        $this->clientRepository = $clientRepository;
        $this->fuelRepository = $fuelRepository;
        $this->snappyPdf = $snappyPdf;
        $this->templating = $templating;
        $this->pathGenerator = $pathGenerator;
        $this->rootPath = $rootPath;
        $this->sourcePath = $sourcePath;
    }

    public function create(Invoice $invoice): File
    {
        $ext = 'pdf';

        $name = $this->generateName($invoice);
        $path = $this->pathGenerator->generate($name, ['pathPrefix' => 'documents']);

        /** @var ShellInformation $shellInfo */
        $shellInfo = $this->shellInfoRepository->find([]);

        if (!$shellInfo instanceof ShellInformation) {
            throw new InvalidArgumentException('ShellInformation not found');
        }

        /** @var Client $client */
        $client = $this->clientRepository->find(['client1CId_equalTo' => $invoice->getClient1CId()]);

        $fuelCodes = [];
        foreach ($invoice->getItems() as $item) {
            $fuelCodes[] = $item->getFuelCode();
        }
        /** @var Type[] $fuel */
        $fuel = $this->fuelRepository->findMany([
            'fuelCode_in' => $fuelCodes,
            IndexByFuelCode::class => true,
        ]);

        $html = $this->templating->render('pdf/invoice.html.twig', [
            'invoice' => $invoice,
            'client' => $client,
            'shellInfo' => $shellInfo,
            'fuel' => $fuel,
            'rootPath' => $this->rootPath,
        ]);

        $options = [
            'encoding' => 'utf-8',
            'images' => true,
            'margin-top' => 3,
            'margin-right' => 0,
            'margin-left' => 3,
            'page-size' => 'A4',
        ];

        $file = new File($path, $name, $ext);

        $this->snappyPdf->generateFromHtml($html, $this->sourcePath.$file->getFile(), $options);

        return $file;
    }

    private function generateName(Invoice $invoice)
    {
        return sprintf(
            '%s_%d',
            sha1($invoice->getNumber()),
            time()
        );
    }
}
