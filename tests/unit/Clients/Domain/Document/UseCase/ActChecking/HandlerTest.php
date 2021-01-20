<?php

namespace Tests\Unit\Clients\Domain\Document\UseCase\ActChecking;

use App\Application\Domain\ValueObject\IdentityId;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Document\Document;
use App\Clients\Domain\Document\Service\ActCheckingFileService;
use App\Clients\Domain\Document\UseCase\ActChecking\Handler;
use App\Clients\Domain\Document\UseCase\ActChecking\HandlerRequest;
use App\Clients\Domain\Document\ValueObject\File;
use App\Clients\Domain\Document\ValueObject\Status;
use App\Clients\Domain\Document\ValueObject\Type;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Tests\Unit\Traits\UuidMock;

final class HandlerTest extends TestCase
{
    use UuidMock;

    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $repositoryMock;
    /**
     * @var ActCheckingFileService|\Prophecy\Prophecy\ObjectProphecy
     */
    private $actCheckingFileServiceMock;
    /**
     * @var ObjectManager|\Prophecy\Prophecy\ObjectProphecy
     */
    private $objectManagerMock;
    /**
     * @var Handler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->prophesize(Repository::class);
        $this->actCheckingFileServiceMock = $this->prophesize(ActCheckingFileService::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler($this->repositoryMock->reveal(), $this->actCheckingFileServiceMock->reveal(), $this->objectManagerMock->reveal());

        $string = '550e8400-e29b-41d4-a716-446655440033';
        $this->setUuid4Mock($string);
    }

    public function tearDown(): void
    {
        Uuid::setFactory(new UuidFactory());
    }

    public function testHandle()
    {
        $clientMock = $this->prophesize(Client::class);
        $clientIdValue = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($clientIdValue);

        $handlerRequest = new HandlerRequest($clientMock->reveal(), '2020-01', '2020-02');

        $file = new File('file/path/', 'filename', 'ext');
        $this->actCheckingFileServiceMock->create(
            $handlerRequest->getClient(),
            $handlerRequest->getDateFrom(),
            $handlerRequest->getDateTo()
        )
            ->shouldBeCalled()
            ->willReturn($file);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');

        Document::create(
            IdentityId::next(),
            $handlerRequest->getClient(),
            $file,
            Type::actChecking(),
            Status::formedByRequest(),
            $dateTime
        );

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEmpty($result->getNumber());
        $this->assertEmpty($result->getAmount());
        $this->assertEquals($file, $result->getFile());
        $actCheckingTypeValue = 1;
        $this->assertEquals($actCheckingTypeValue, $result->getType());

        $formedByRequestStatusValue = 1;
        $this->assertEquals($formedByRequestStatusValue, $result->getStatus());
    }
}
