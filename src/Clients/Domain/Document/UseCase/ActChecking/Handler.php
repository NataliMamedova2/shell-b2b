<?php

namespace App\Clients\Domain\Document\UseCase\ActChecking;

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Document\Document;
use App\Clients\Domain\Document\Service\ActCheckingFileService;
use App\Clients\Domain\Document\ValueObject\Status;
use App\Clients\Domain\Document\ValueObject\Type;
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
     * @var ActCheckingFileService
     */
    private $actCheckingFileService;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Repository $repository,
        ActCheckingFileService $actCheckingFileService,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->actCheckingFileService = $actCheckingFileService;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $file = $this->actCheckingFileService->create(
            $handlerRequest->getClient(),
            $handlerRequest->getDateFrom(),
            $handlerRequest->getDateTo()
        );

        $entity = Document::create(
            IdentityId::next(),
            $handlerRequest->getClient(),
            $file,
            Type::actChecking(),
            Status::formedByRequest(),
            new \DateTimeImmutable()
        );

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
