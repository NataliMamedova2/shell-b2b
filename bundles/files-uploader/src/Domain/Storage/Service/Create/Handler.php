<?php

namespace FilesUploader\Domain\Storage\Service\Create;

use FilesUploader\Domain\Storage\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use FilesUploader\Domain\Storage\File;
use FilesUploader\Domain\Storage\ValueObject\Id;
use FilesUploader\Domain\Storage\ValueObject\IpAddress;

final class Handler
{

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RepositoryInterface $repository, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->request = $requestStack->getCurrentRequest();
        $this->entityManager = $entityManager;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        $entity = File::create(
            Id::next(),
            $command->fileName,
            $command->path,
            $command->extension,
            $command->originalName,
            $command->type,
            $command->size,
            $command->metaInfo,
            IpAddress::fromString(($this->request) ? $this->request->getClientIp() : '127.0.0.1')
        );

        $this->repository->add($entity);

        $this->entityManager->flush();
    }
}
