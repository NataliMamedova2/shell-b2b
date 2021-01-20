<?php

namespace Tests\Unit\Clients\Infrastructure\User\Repository;

use PHPUnit\Framework\TestCase;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Infrastructure\User\Repository\UserRepositoryFactory;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;

final class UserRepositoryFactoryTest extends TestCase
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
        $factory = new UserRepositoryFactory();

        $result = $factory->__invoke($this->entityManagerMock->reveal(), $this->criteriaFactoryMock->reveal());

        $this->assertInstanceOf(UserRepository::class, $result);
    }
}
