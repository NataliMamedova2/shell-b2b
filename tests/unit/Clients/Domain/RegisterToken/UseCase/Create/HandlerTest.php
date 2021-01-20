<?php

namespace Tests\Unit\Clients\Domain\RegisterToken\UseCase\Create;

use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\Agent1CId;
use App\Clients\Domain\Client\ValueObject\ClientId;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Client\ValueObject\EdrpouInn;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Client\ValueObject\NktId;
use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use App\Clients\Domain\RegisterToken\Register;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use App\Clients\Domain\RegisterToken\Service\TokenGenerator;
use App\Clients\Domain\RegisterToken\UseCase\Create\Handler;
use App\Clients\Domain\RegisterToken\UseCase\Create\HandlerRequest;
use App\Clients\Domain\RegisterToken\ValueObject\RegisterId;
use App\Clients\Domain\RegisterToken\ValueObject\Token;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\DomainException;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Tests\Unit\Clients\Domain\RegisterToken\RegisterTest;
use Tests\Unit\Traits\UuidMock;

final class HandlerTest extends TestCase
{
    use UuidMock;

    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $repositoryMock;
    /**
     * @var ObjectManager|\Prophecy\Prophecy\ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var TokenGenerator|\Prophecy\Prophecy\ObjectProphecy
     */
    private $tokenGeneratorMock;
    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(RegisterRepository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);
        $this->tokenGeneratorMock = $this->prophesize(TokenGenerator::class);

        $this->handler = new Handler(
            $this->repositoryMock->reveal(),
            $this->tokenGeneratorMock->reveal(),
            $this->objectManagerMock->reveal()
        );
    }

    public function tearDown(): void
    {
        Uuid::setFactory(new UuidFactory());
    }

    public function testHandleValidTokenForEmailExistReturnException(): void
    {
        $handlerRequest = $this->getHandleRequest();

        $token = RegisterTest::validEntity();

        $this->repositoryMock->find(['client_equalTo' => $handlerRequest->client])
            ->shouldBeCalled()
            ->willReturn($token);

        $this->tokenGeneratorMock->generate()
            ->shouldNotBeCalled();
        $this->repositoryMock->add($token)
            ->shouldNotBeCalled();
        $this->objectManagerMock->flush()
            ->shouldNotBeCalled();

        $this->expectException(DomainException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleTokenForEmailNotExistReturnObject(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440033';
        $this->setUuid4Mock($string);

        $handlerRequest = $this->getHandleRequest();

//        $this->repositoryMock->find(['email_equalTo' => $handlerRequest->email])
//            ->shouldBeCalled()
//            ->willReturn(null);

        $token = 'securetoken';
        $this->tokenGeneratorMock->generate()
            ->shouldBeCalled()
            ->willReturn($token);

        $entity =  Register::create(
            RegisterId::next(),
            $this->getClient(),
            new Email($handlerRequest->email),
            new Token($token),
            new \DateTimeImmutable()
        );

//        $this->repositoryMock->add($entity)
//            ->shouldBeCalled();

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($entity->getToken()->getToken(), $result->getToken()->getToken());
    }

    private function getHandleRequest(): HandlerRequest
    {
        $handlerRequest = new HandlerRequest();

        $handlerRequest->client = $this->getClient();
        $handlerRequest->email = 'email@email.com';

        return $handlerRequest;
    }

    private function getClient(): Client
    {
        $identity = ClientId::next();

        $clientId = new Client1CId('КВ-0004888');
        $fullName = new FullName('Лавров Олександр Генадійович');
        $edrpouInn = new EdrpouInn('24584810');
        $type = new Type(0);
        $nktId = new NktId(9180004888);
        $managerId = new Manager1CId('КВЦ0000161');
        $agentId = new Agent1CId('');
        $fcCbrId = new FcCbrId(12435);
        $status = new Status(1);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');
        $entity = Client::create(
            $identity,
            $clientId,
            $fullName,
            $edrpouInn,
            $type,
            $nktId,
            $managerId,
            $agentId,
            $fcCbrId,
            $status,
            $dateTime
        );

        return $entity;
    }
}
