<?php

namespace App\Clients\Domain\User\UseCase\Register;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Company\ValueObject\CompanyId;
use App\Clients\Domain\User\Repository\UserRepository as CompanyUserRepository;
use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\UserId;
use App\Clients\Domain\User\ValueObject\Username;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\DomainException;
use Domain\Interfaces\Handler as DomainHandler;
use Infrastructure\Interfaces\Repository\Repository;

final class Handler implements DomainHandler
{
    /**
     * @var CompanyUserRepository
     */
    private $companyUserRepository;

    /**
     * @var Repository
     */
    private $companyRepository;

    /**
     * @var PasswordEncoder
     */
    private $passwordEncoder;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        CompanyUserRepository $companyUserRepository,
        Repository $companyRepository,
        PasswordEncoder $passwordEncoder,
        ObjectManager $objectManager
    ) {
        $this->companyUserRepository = $companyUserRepository;
        $this->companyRepository = $companyRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->objectManager = $objectManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        if ($this->companyUserRepository->findByUsernameOrEmail($handlerRequest->username, $handlerRequest->email)) {
            throw new DomainException(sprintf('User "%s" already exist', $handlerRequest->username));
        }

        $company = $this->companyRepository->find(['client_equalTo' => $handlerRequest->client]);
        if (!$company) {
            $company = Company::register(
                CompanyId::next(),
                $handlerRequest->client,
                new Email($handlerRequest->email),
                new \DateTimeImmutable()
            );
            //throw new DomainException(sprintf('Company for client %s already exist', $handlerRequest->client->getClient1CId()));
        }

        $passwordHash = $this->passwordEncoder->encode($handlerRequest->password);

        $entity = User::create(
            UserId::next(),
            $company,
            new Email($handlerRequest->email),
            new Username($handlerRequest->username),
            $passwordHash,
            new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName),
            Role::admin(),
            new Phone($handlerRequest->phone)
        );

        $this->companyUserRepository->add($entity);
        $this->objectManager->flush();

        return $entity;
    }
}
