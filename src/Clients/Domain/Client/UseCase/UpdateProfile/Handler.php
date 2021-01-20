<?php

namespace App\Clients\Domain\Client\UseCase\UpdateProfile;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Domain\ValueObject\Phone;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\MoneyLimits;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Card\ValueObject\TimeUse;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Company\ValueObject\Accounting;
use App\Clients\Domain\Company\ValueObject\Name;
use App\Clients\Domain\Company\ValueObject\PostalAddress;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\FuelLimit\ValueObject\Limits;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;

final class Handler implements DomainHandler
{
    /**
     * @var Repository
     */
    private $clientRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Repository $clientRepository,
        ObjectManager $objectManager
    ) {
        $this->clientRepository = $clientRepository;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        /** @var Client $client */
        $client = $this->clientRepository->findById($handlerRequest->getId());

        $company = $client->getCompany();

        $company->update(
            new Name($handlerRequest->name),
            new Accounting(
                !empty($handlerRequest->accountingEmail) ? new Email($handlerRequest->accountingEmail) : null,
                !empty($handlerRequest->accountingPhone) ? new Phone($handlerRequest->accountingPhone) : null
            ),
            new PostalAddress($handlerRequest->postalAddress)
        );

        $company->setEmail(new Email($handlerRequest->email));

        $this->objectManager->flush();

        return $client;
    }
}
