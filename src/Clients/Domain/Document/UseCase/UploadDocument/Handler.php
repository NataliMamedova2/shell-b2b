<?php

namespace App\Clients\Domain\Document\UseCase\UploadDocument;

use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Document\Document;
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
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Repository $repository,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $clientId = $handlerRequest->getClient()->getClient1CId();

        $document = Document::createUploadedDocument(
            new Client1CId($clientId),
            $handlerRequest->getType(),
            $handlerRequest->getFile(),
            new \DateTimeImmutable()
        );

        $this->repository->add($document);
        $this->objectManager->flush();

        return $document;
    }
}
