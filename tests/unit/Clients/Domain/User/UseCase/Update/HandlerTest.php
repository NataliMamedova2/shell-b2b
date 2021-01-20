<?php

namespace Tests\Unit\Clients\Domain\User\UseCase\Update;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\UseCase\Update\Handler;
use App\Clients\Domain\User\UseCase\Update\HandlerRequest;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Username;
use App\Users\Infrastructure\Criteria\UsernameOrEmail;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\User\UserTest;

final class HandlerTest extends TestCase
{
    /**
     * @var UserRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $userRepositoryMock;
    /**
     * @var ObjectManager|\Prophecy\Prophecy\ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var Handler
     */
    private $handler;
    /**
     * @var PasswordEncoder|\Prophecy\Prophecy\ObjectProphecy
     */
    private $passwordEncoderMock;

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
    }

    public function testHandleEntityNotFoundReturnException(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = $this->getHandleRequest();
        $handlerRequest->setId($string);

        $entity = null;
        $this->userRepositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->passwordEncoderMock->encode($handlerRequest->password)
            ->shouldNotBeCalled();

        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(EntityNotFoundException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleUsernameExistReturnException(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = $this->getHandleRequest();
        $handlerRequest->setId($string);

        $entity = UserTest::createValidEntity();
        $this->userRepositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $userWithUsername = UserTest::createValidEntity();
        $this->userRepositoryMock->find([
            UsernameOrEmail::class => [
                'email' => $handlerRequest->email,
                'username' => $handlerRequest->username,
            ],
            'id_notEqualTo' => $handlerRequest->getId(),
        ])
        ->shouldBeCalled()
        ->willReturn($userWithUsername);

        $this->passwordEncoderMock->encode($handlerRequest->password)
            ->shouldNotBeCalled();

        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(DomainException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleUpdateUserReturnEntity(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';

        $handlerRequest = $this->getHandleRequest();
        $handlerRequest->setId($string);

        $entity = UserTest::createValidEntity();
        $this->userRepositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->userRepositoryMock->find([
            UsernameOrEmail::class => [
                'email' => $handlerRequest->email,
                'username' => $handlerRequest->username,
            ],
            'id_notEqualTo' => $handlerRequest->getId(),
        ])
        ->shouldBeCalled()
        ->willReturn(null);

        $passwordHash = 'passwordHash';
        $this->passwordEncoderMock->encode($handlerRequest->password)
            ->shouldBeCalled()
            ->willReturn($passwordHash);

        $entity->update(
            new Email($handlerRequest->email),
            new Username($handlerRequest->username),
            new Name($handlerRequest->firstName, $handlerRequest->middleName, $handlerRequest->lastName),
            Role::fromName($handlerRequest->role),
            new Phone($handlerRequest->phone)
        );

        $entity->changePassword($passwordHash);

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($handlerRequest->email, (string) $result->getEmail());
        $this->assertEquals($handlerRequest->username, (string) $result->getUsername());
    }

    private function getHandleRequest(): HandlerRequest
    {
        $handlerRequest = new HandlerRequest();

        $handlerRequest->username = 'username';
        $handlerRequest->email = 'email@email.com';
        $handlerRequest->password = '111';
        $handlerRequest->firstName = 'firstName';
        $handlerRequest->lastName = 'lastName';
        $handlerRequest->middleName = 'middleName';
        $handlerRequest->role = 'admin';
        $handlerRequest->phone = '+34567890';

        return $handlerRequest;
    }
}
