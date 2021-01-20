<?php

namespace Tests\Unit\Api\Action\Api\V1\Company;

use App\Api\Action\Api\V1\Company\DashboardAction;
use App\Api\Crud\Interfaces\Response;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Driver\ValueObject\Status as DriverStatus;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Status;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class DashboardActionTest extends TestCase
{
    /**
     * @var MyselfInterface|ObjectProphecy
     */
    private $myselfMock;
    /**
     * @var UserRepository|ObjectProphecy
     */
    private $userRepositoryMock;
    /**
     * @var UserRepository|ObjectProphecy
     */
    private $driverRepositoryMock;
    /**
     * @var Response|ObjectProphecy
     */
    private $jsonResponseMock;
    /**
     * @var DashboardAction
     */
    private $serviceAction;

    protected function setUp(): void
    {
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->userRepositoryMock = $this->prophesize(UserRepository::class);
        $this->driverRepositoryMock = $this->prophesize(Repository::class);
        $this->jsonResponseMock = $this->prophesize(Response::class);

        $this->serviceAction = new DashboardAction(
            $this->myselfMock->reveal(),
            $this->userRepositoryMock->reveal(),
            $this->driverRepositoryMock->reveal(),
            $this->jsonResponseMock->reveal()
        );
    }

    public function testInvokeReturnSymfonyResponse(): void
    {
        $userMock = $this->prophesize(User::class);
        $this->myselfMock->get()
            ->shouldBeCalled()
            ->willReturn($userMock);

        $companyMock = $this->prophesize(Company::class);
        $this->myselfMock->getCompany()
            ->shouldBeCalled()
            ->willReturn($companyMock);

        $userId = '550e8400-e29b-41d4-a716-446655440000';
        $userMock->getId()
            ->shouldBeCalled()
            ->willReturn($userId);

        $usersCount = 12;
        $this->userRepositoryMock->count([
            'company_equalTo' => $companyMock->reveal(),
            'id_notEqualTo' => $userId,
            'status_equalTo' => Status::active()->getValue(),
        ])
            ->shouldBeCalled()
            ->willReturn($usersCount);

        $clientMock = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $client1CId = 'clientId';
        $clientMock->getClient1CId()
            ->shouldBeCalled()
            ->willReturn($client1CId);

        $driversCount = 3;
        $this->driverRepositoryMock->count([
            'client1CId_equalTo' => $client1CId,
            'status_equalTo' => DriverStatus::active()->getValue(),
        ])
            ->shouldBeCalled()
            ->willReturn($driversCount);

        $data = [
            'usersCount' => $usersCount,
            'driversCount' => $driversCount,
        ];

        $responseMock = \Symfony\Component\HttpFoundation\Response::create(json_encode($data));

        $this->jsonResponseMock->createSuccessResponse($data)
            ->shouldBeCalled()
            ->willReturn($responseMock);

        $result = $this->serviceAction->__invoke();

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $result);
        $this->assertEquals(json_encode($data), $result->getContent());
    }
}
