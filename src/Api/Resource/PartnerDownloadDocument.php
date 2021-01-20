<?php

namespace App\Api\Resource;

use App\Clients\Domain\Document\Document as DomainDocument;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PartnerDownloadDocument implements Model
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public $name;
    public $link;

    /**
     * @param DomainDocument $document
     */
    public function prepare($document): Model
    {
        $file = $document->getFile();
        $filePath = $this->urlGenerator->generate('api_v1_partners_documents_download', [
            'id' => $document->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->name = $file->getNameWithExtension();
        $this->link = $filePath;

        return $this;
    }
}
