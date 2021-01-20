<?php

namespace Tests\Unit\Clients\Infrastructure\RegisterToken\Repository;

use PHPUnit\Framework\TestCase;
use App\Clients\Infrastructure\RegisterToken\Repository\RegisterRepositoryFactory;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;

final class RegisterRepositoryFactoryTest extends TestCase
{

    /**
     * @var EntityManagerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $entityManagerMock;
    /**
     * @var CriteriaFactory|\Prophecy\Prophecy\ObjectProphecy
     */
    private $criteriaFactoryMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->criteriaFactoryMock = $this->prophesize(CriteriaFactory::class);
    }

    public function testInvokeReturnRepository(): void
    {
        $factory = new RegisterRepositoryFactory();

        $result = $factory->__invoke($this->entityManagerMock->reveal(), $this->criteriaFactoryMock->reveal());

        $this->assertInstanceOf(RegisterRepository::class, $result);
    }
}
