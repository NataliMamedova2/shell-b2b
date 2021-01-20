<?php

namespace App\Api\Resource;

use App\Api\Resource\Traits\PopulateObject;
use App\Clients\Domain\Document\Document as DomainDocument;
use App\Clients\Domain\Document\ValueObject\Status;
use App\Clients\Domain\Document\ValueObject\Type;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class Document implements Model
{
    use PopulateObject;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public $id;
    public $number;
    public $file;
    public $amount;
    public $type;
    public $status;
    public $createdAt;

    /**
     * @param DomainDocument $document
     *
     * @return Model
     */
    public function prepare($document): Model
    {
        $this->populateObject($document);

        $this->type = (new Type($document->getType()))->getName();
        $this->status = (new Status($document->getStatus()))->getName();

        $file = $document->getFile();
        $filePath = $this->urlGenerator->generate('api_v1_documents_download', [
            'id' => $document->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->file = [
            'name' => $file->getNameWithExtension(),
            'link' => $filePath,
        ];

        return $this;
    }
}
