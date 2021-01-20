<?php

namespace Tests\Unit\Clients\Domain\Driver\UseCase\Create;

use App\Clients\Domain\Driver\UseCase\Create\Handler;
use App\Clients\Domain\Driver\UseCase\Create\HandlerRequest;
use App\Clients\Domain\Driver\ValueObject\Status;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\Unit\Clients\Domain\Client\ClientTest;

final class HandlerTest extends TestCase
{
    /**
     * @var Repository|ObjectProphecy
     */
    private $repositoryMock;
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
        $this->repositoryMock = $this->prophesize(Repository::class);
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler($this->repositoryMock->reveal(), $this->objectManagerMock->reveal());
    }

    public function testHandleWithRequiredDataReturnEntity()
    {
        $handlerRequest = new HandlerRequest();

        $client = ClientTest::createValidEntity();
        $handlerRequest->client = $client;
        $handlerRequest->firstName = 'firstName';
        $handlerRequest->lastName = 'lastName';
        $handlerRequest->status = Status::active()->getName();
        $handlerRequest->phones = [
            ['number' => '+380987655433'],
        ];

        $handlerRequest->middleName = '';
        $handlerRequest->email = '';
        $handlerRequest->note = '';
        $handlerRequest->carsNumbers = [];

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($handlerRequest->middleName, $result->getName()->getMiddleName());
        $this->assertEquals($handlerRequest->email, $result->getEmail());
        $this->assertEquals($handlerRequest->note, $result->getNote());
        $this->assertCount(1, $result->getPhones());
        $this->assertCount(0, $result->getCarNumbers());
    }

    public function testHandleWithCarNumbersReturnEntity()
    {
        $handlerRequest = new HandlerRequest();

        $client = ClientTest::createValidEntity();
        $handlerRequest->client = $client;
        $handlerRequest->firstName = 'firstName';
        $handlerRequest->lastName = 'lastName';
        $handlerRequest->status = Status::active()->getName();
        $handlerRequest->phones = [
            ['number' => '+380987655433'],
        ];

        $handlerRequest->carsNumbers = [
            ['number' => 'AA12333'],
            ['number' => 'DD422'],
        ];

        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $result = $this->handler->handle($handlerRequest);

        $this->assertEquals($handlerRequest->middleName, $result->getName()->getMiddleName());
        $this->assertEquals($handlerRequest->email, $result->getEmail());
        $this->assertEquals($handlerRequest->note, $result->getNote());
        $this->assertCount(1, $result->getPhones());
        $this->assertCount(2, $result->getCarNumbers());
    }
}
