<?php

namespace Tests\Unit\Clients\Domain\User\UseCase\Create;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\Agent1CId;
use App\Application\Domain\ValueObject\Client1CId;
use App\Clients\Domain\Client\ValueObject\ClientId;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Client\ValueObject\NktId;
use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Company\ValueObject\CompanyId;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\UseCase\Create\Handler;
use App\Clients\Domain\User\UseCase\Create\HandlerRequest;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\UserId;
use App\Clients\Domain\User\ValueObject\Username;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\DomainException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Tests\Unit\Traits\UuidMock;
use Tests\Unit\Users\Domain\User\UserTest;

final class HandlerTest extends TestCase
{
    use UuidMock;

    /**
     * @var UserRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $userRepositoryMock;
    /**
     * @var ObjectManager|\Prophecy\Prophecy\ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var PasswordEncoder|\Prophecy\Prophecy\ObjectProphecy
     */
    private $passwordEncoderMock;

    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->prophesize(UserRepository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);
        $this->passwordEncoderMock = $this->prophesize(PasswordEncoder::class);

        $this->handler = new Handler(
            $this->userRepositoryMock->reveal(),
            $this->passwordEncoderMock->reveal(),
            $this->objectManagerMock->reveal()
        );

        $string = '550e8400-e29b-41d4-a716-446655440033';
        $this->setUuid4Mock($string);
    }

    public function tearDown(): void
    {
        Uuid::setFactory(new UuidFactory());
    }

    public function testHandleUserExistReturnException()
    {
        $handlerRequest = $this->getHandleRequest();

        $email = new Email($handlerRequest->email);
        $username = new Username($handlerRequest->username);
        $password = '';
        $name = new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName);
        $role = Role::fromName($handlerRequest->role);
        $phone = new Phone($handlerRequest->phone);

        $entity = User::create(
            UserId::next(),
            $handlerRequest->company,
            $email,
            $username,
            $password,
            $name,
            $role,
            $phone
        );

        $this->userRepositoryMock->findByUsernameOrEmail($handlerRequest->username, $handlerRequest->email)
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->passwordEncoderMock->encode($handlerRequest->password)
            ->shouldNotBeCalled();

        $this->userRepositoryMock->add($entity)
            ->shouldNotBeCalled();
        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(DomainException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleUserNotExistReturnException()
    {
        $handlerRequest = $this->getHandleRequest();

//        $this->userRepositoryMock->findByUsernameOrEmail($handlerRequest->username, $handlerRequest->email)
//            ->shouldBeCalled()
//            ->willReturn(null);

        $passwordHash = 'passwordHash';
        $this->passwordEncoderMock->encode($handlerRequest->password)
            ->shouldBeCalled()
            ->willReturn($passwordHash);

        $email = new Email($handlerRequest->email);
        $username = new Username($handlerRequest->username);
        $name = new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName);
        $role = Role::fromName($handlerRequest->role);
        $phone = new Phone($handlerRequest->phone);

        $entity = User::create(
            UserId::next(),
            $handlerRequest->company,
            $email,
            $username,
            $passwordHash,
            $name,
            $role,
            $phone
        );

//        $this->userRepositoryMock->add($entity)
//            ->shouldBeCalled();
        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($handlerRequest->email, (string) $result->getEmail());
        $this->assertEquals($handlerRequest->username, (string) $result->getUsername());
    }

    private function getHandleRequest(): HandlerRequest
    {
        $companyMock = $this->prophesize(Company::class);

        $handlerRequest = new HandlerRequest();

        $handlerRequest->username = 'username';
        $handlerRequest->email = 'email@email.com';
        $handlerRequest->company = $companyMock->reveal();
        $handlerRequest->password = '111';
        $handlerRequest->firstName = 'firstName';
        $handlerRequest->lastName = 'lastName';
        $handlerRequest->middleName = 'middleName';
        $handlerRequest->role = 'admin';
        $handlerRequest->phone = '+34567890';

        return $handlerRequest;
    }
}
