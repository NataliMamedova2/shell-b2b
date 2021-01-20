<?php

namespace FilesUploader\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FilesUploader\Domain\Storage\File;
use FilesUploader\Domain\Storage\Repository\RepositoryInterface;

final class StorageRepository implements RepositoryInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(File $entity)
    {
        $this->entityManager->persist($entity);
    }
}
