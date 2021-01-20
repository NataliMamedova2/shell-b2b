<?php

namespace Tests\Unit\Clients\Domain\User\UseCase\Register;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Company\ValueObject\CompanyId;
use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\UseCase\Register\Handler;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\UseCase\Register\HandlerRequest;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\UserId;
use App\Clients\Domain\User\ValueObject\Username;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\DomainException;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class HandlerTest extends TestCase
{
    /**
     * @var UserRepository|ObjectProphecy
     */
    private $companyUserRepositoryMock;
    /**
     * @var Repository|ObjectProphecy
     */
    private $companyRepositoryMock;
    /**
     * @var PasswordEncoder|ObjectProphecy
     */
    private $passwordEncoderMock;
    /**
     * @var ObjectManager|ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->companyUserRepositoryMock = $this->prophesize(UserRepository::class);
        $this->companyRepositoryMock = $this->prophesize(Repository::class);
        $this->passwordEncoderMock = $this->prophesize(PasswordEncoder::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler(
            $this->companyUserRepositoryMock->reveal(),
            $this->companyRepositoryMock->reveal(),
            $this->passwordEncoderMock->reveal(),
            $this->objectManagerMock->reveal()
        );
    }

    public function testHandleUserAlreadyExistReturnException()
    {
        $handlerRequest = new HandlerRequest();
        $handlerRequest->username = 'username';
        $handlerRequest->email = 'email';

        $userMock = $this->prophesize(User::class);
        $this->companyUserRepositoryMock->findByUsernameOrEmail($handlerRequest->username, $handlerRequest->email)
            ->shouldBeCalled()
            ->willReturn($userMock);

        $this->companyRepositoryMock->find(['client_equalTo' => $handlerRequest->client])
            ->shouldNotBeCalled();
        $this->passwordEncoderMock->encode($handlerRequest->password)
            ->shouldNotBeCalled();
        $this->companyUserRepositoryMock->add()
            ->shouldNotBeCalled();
        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(DomainException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleReturnUserObject()
    {
        $handlerRequest = new HandlerRequest();
        $handlerRequest->username = 'username';
        $handlerRequest->email = 'email@email.com';
        $handlerRequest->password = 'password';
        $handlerRequest->firstName = 'firstName';
        $handlerRequest->middleName = 'middleName';
        $handlerRequest->lastName = 'lastName';
        $handlerRequest->phone = '+380933773333';

        $clientMock = $this->prophesize(Client::class);
        $handlerRequest->client = $clientMock->reveal();

        $this->companyRepositoryMock->find(['client_equalTo' => $handlerRequest->client])
            ->shouldBeCalled()
            ->willReturn(null);

        $passwordHash = 'passwordHash';
        $this->passwordEncoderMock->encode($handlerRequest->password)
            ->shouldBeCalled()
            ->willReturn($passwordHash);

        $clientFullName = 'Client full name';
        $clientMock->getFullName()
            ->shouldBeCalled()
            ->willReturn($clientFullName);
        $company = Company::register(
            CompanyId::next(),
            $handlerRequest->client,
            new Email($handlerRequest->email),
            new \DateTimeImmutable('2019-01-01 00:00:00')
        );
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

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $this->handler->handle($handlerRequest);
    }
}
