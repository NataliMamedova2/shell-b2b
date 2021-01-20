<?php

namespace App\Api\DataTransformer;

use App\Api\Crud\Interfaces\DataTransformer;
use App\Api\Resource\DownloadDocument;
use App\Api\Resource\Model;
use App\Api\Resource\PartnerDownloadDocument;
use App\Clients\Domain\Document\Document;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PartnerDownloadDocumentDataTransformer implements DataTransformer
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Document $document
     *
     * @return Model
     */
    public function transform($document)
    {
        if (!$document instanceof Document) {
            throw new \InvalidArgumentException();
        }

        $model = new PartnerDownloadDocument($this->urlGenerator);

        return $model->prepare($document);
    }
}
