<?php

namespace Tests\Unit\Api\Action\Api\V1\Supplies\ListAction;

use App\Api\Action\Api\V1\Supplies\ListAction\ListService;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Infrastructure\Fuel\Criteria\FuelWithPrice;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\Fuel\Price\PriceTest;
use Tests\Unit\Clients\Domain\Fuel\Type\TypeTest;

final class ListServiceTest extends TestCase
{
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $fuelTypeRepositoryMock;
    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $fuelPriceRepositoryMock;
    /**
     * @var QueryRequest|\Prophecy\Prophecy\ObjectProphecy
     */
    private $queryRequestMock;
    /**
     * @var ListService
     */
    private $service;

    protected function setUp(): void
    {
        $this->fuelTypeRepositoryMock = $this->prophesize(Repository::class);
        $this->fuelPriceRepositoryMock = $this->prophesize(Repository::class);
        $this->queryRequestMock = $this->prophesize(QueryRequest::class);

        $this->service = new ListService($this->fuelTypeRepositoryMock->reveal(), $this->fuelPriceRepositoryMock->reveal());
    }

    public function testHandleNoFuelTypesFoundReturnEmptyCollection(): void
    {
        $criteria = [
            FuelWithPrice::class => true,
        ];
        $orderCriteria = ['fuelName' => 'ASC'];

        $this->fuelTypeRepositoryMock->findMany($criteria, $orderCriteria)
            ->shouldBeCalled()
            ->willReturn([]);
        $this->fuelPriceRepositoryMock->find([])
            ->shouldNotBeCalled();

        $result = $this->service->handle($this->queryRequestMock->reveal());

        $this->assertEquals([], $result);
    }

    public function testHandleFoundFuelTypesReturnCollection(): void
    {
        $criteria = [
            FuelWithPrice::class => true,
        ];
        $orderCriteria = ['fuelName' => 'ASC'];

        /** @var Type[] $collection */
        $collection = [
            TypeTest::createValidEntity(),
        ];
        $this->fuelTypeRepositoryMock->findMany($criteria, $orderCriteria)
            ->shouldBeCalled()
            ->willReturn($collection);

        $this->fuelPriceRepositoryMock->find([
            'fuelCode_equalTo' => $collection[0]->getFuelCode(),
            'fuelPrice_greaterThan' => 0,
        ])
        ->shouldBeCalledTimes(1)
        ->willReturn(PriceTest::createValidEntity());

        $result = $this->service->handle($this->queryRequestMock->reveal());

        $this->assertEquals($collection[0]->getFuelName(), $result[0]['name']);
    }

    public function testHandleFoundFuelTypesWithoutPriceReturnEmptyCollection(): void
    {
        $criteria = [
            FuelWithPrice::class => true,
        ];
        $orderCriteria = ['fuelName' => 'ASC'];

        /** @var Type[] $collection */
        $collection = [
            TypeTest::createValidEntity(),
        ];
        $this->fuelTypeRepositoryMock->findMany($criteria, $orderCriteria)
            ->shouldBeCalled()
            ->willReturn($collection);

        $this->fuelPriceRepositoryMock->find([
            'fuelCode_equalTo' => $collection[0]->getFuelCode(),
            'fuelPrice_greaterThan' => 0,
        ])
        ->shouldBeCalledTimes(1);

        $result = $this->service->handle($this->queryRequestMock->reveal());

        $this->assertEquals([], $result);
    }
}
