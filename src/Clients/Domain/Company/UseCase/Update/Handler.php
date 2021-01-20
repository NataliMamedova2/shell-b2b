<?php

namespace App\Clients\Domain\Company\UseCase\Update;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Company\ValueObject\Accounting;
use App\Clients\Domain\Company\ValueObject\Name;
use App\Clients\Domain\Company\ValueObject\PostalAddress;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
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
        /** @var Company|null $entity */
        $entity = $this->repository->findById($handlerRequest->getId());

        if (!$entity instanceof Company) {
            throw new EntityNotFoundException('Entity not found.');
        }

        $email = $handlerRequest->accountingEmail ? new Email($handlerRequest->accountingEmail) : null;
        $phone = new Phone($handlerRequest->accountingPhone);

        $legalName = $entity->getClient()->getFullName();
        $name = $handlerRequest->name ? $handlerRequest->name : $legalName;

        $entity->update(
            new Name($name),
            new Accounting($email, $phone),
            $handlerRequest->postalAddress ? new PostalAddress($handlerRequest->postalAddress) : null
        );

        $this->repository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
