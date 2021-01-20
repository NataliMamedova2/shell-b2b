<?php

namespace Tests\Unit\Api\Action\Api\V1\Me\ProfileAction;

use App\Api\Action\Api\V1\Me\ProfileAction\ProfileAction;
use App\Api\Crud\Interfaces\Response;
use App\Api\Resource\Model;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\User;
use App\Security\Cabinet\MyselfInterface;
use App\Users\Domain\User\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class ProfileActionTest extends TestCase
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
     * @var Model|ObjectProphecy
     */
    private $profileResourceMock;
    /**
     * @var Response|ObjectProphecy
     */
    private $jsonResponseMock;
    /**
     * @var ProfileAction
     */
    private $action;

    protected function setUp(): void
    {
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->userRepositoryMock = $this->prophesize(UserRepository::class);
        $this->profileResourceMock = $this->prophesize(Model::class);
        $this->jsonResponseMock = $this->prophesize(Response::class);

        $this->action = new ProfileAction(
            $this->myselfMock->reveal(),
            $this->userRepositoryMock->reveal(),
            $this->profileResourceMock->reveal(),
            $this->jsonResponseMock->reveal()
        );
    }

    public function testInvokeReturnResponce(): void
    {
        $user = $this->prophesize(User::class);
        $this->myselfMock->get()
            ->shouldBeCalled()
            ->willReturn($user->reveal());

        $company = $this->prophesize(Company::class);
        $this->myselfMock->getCompany()
            ->shouldBeCalled()
            ->willReturn($company->reveal());

        $client = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client->reveal());

        $manager1CId = 'manager1CId';
        $client->getManager1CId()
            ->shouldBeCalled()
            ->willReturn($manager1CId);

        $manager = $this->prophesize(\App\Users\Domain\User\User::class);
        $this->userRepositoryMock->find([
            'manager1CId_equalTo' => $manager1CId,
        ])
            ->shouldBeCalled()
            ->willReturn($manager);

        $data = $this->prophesize(Model::class);
        $this->profileResourceMock->prepare([
            'user' => $user,
            'company' => $company,
            'manager' => $manager,
        ])
        ->shouldBeCalled()
        ->willReturn($data);

        $response = $this->prophesize(\Symfony\Component\HttpFoundation\Response::class);
        $this->jsonResponseMock->createSuccessResponse($data)
            ->shouldBeCalled()
            ->willReturn($response);

        $this->action->__invoke();
    }
}
