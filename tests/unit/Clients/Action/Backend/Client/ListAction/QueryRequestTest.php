<?php

namespace Tests\Unit\Clients\Action\Backend\Client\ListAction;

use App\Clients\Action\Backend\Client\ListAction\QueryRequest;
use App\Clients\Infrastructure\Client\Criteria\ClientIdLike;
use App\Clients\Infrastructure\Client\Criteria\FullNameLike;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tests\Unit\Users\Domain\User\UserTest;

final class QueryRequestTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|RequestStack
     */
    private $requestStackMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|TokenStorageInterface
     */
    private $tokenStorageMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|AuthorizationCheckerInterface
     */
    private $authorizationCheckerMock;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|TokenInterface
     */
    private $tokenMock;

    protected function setUp(): void
    {
        $this->requestStackMock = $this->prophesize(RequestStack::class);
        $this->tokenStorageMock = $this->prophesize(TokenStorageInterface::class);
        $this->authorizationCheckerMock = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->tokenMock = $this->prophesize(TokenInterface::class);
    }

    public function testInvokeRequestReturnQueryRequest(): void
    {
        $clientIdValue = 'КВ-0000001';
        $managerIdValue = 'КВЦ0000001';
        $clientNameValue = 'Олександр';
        $statusValue = 0;
        $typeValue = 0;
        $parameters = [
            'clientId' => $clientIdValue,
            'managerId' => $managerIdValue,
            'clientName' => $clientNameValue,
            'status' => (string) $statusValue,
            'type' => (string) $typeValue,
        ];
        $request = Request::create('/', 'GET', $parameters);

        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);

        $this->tokenStorageMock->getToken()
            ->shouldBeCalled()
            ->willReturn($this->tokenMock);
        $user = UserTest::createValidEntity();
        $this->tokenMock->getUser()
            ->shouldBeCalled()
            ->willReturn($user);

        $queryRequest = new QueryRequest($this->requestStackMock->reveal(), $this->tokenStorageMock->reveal(), $this->authorizationCheckerMock->reveal());

        $criteria = [
            ClientIdLike::class => $clientIdValue,
            FullNameLike::class => $clientNameValue,
            'status_equalTo' => (int) $statusValue,
            'type_equalTo' => (int) $typeValue,
        ];

        $orderCriteria = [
            'createdAt' => 'DESC',
            'fullName' => 'ASC',
        ];
        $this->assertEquals($criteria, $queryRequest->getCriteria());
        $this->assertEquals($parameters, $queryRequest->getData());
        $this->assertEquals($orderCriteria, $queryRequest->getOrder());
        $this->assertEquals(1, $queryRequest->getPage());
    }

    public function testInvokeRequestEmptyCriteriaReturnQueryRequest(): void
    {
        $parameters = [];
        $request = Request::create('/', 'GET', $parameters);

        $this->requestStackMock->getCurrentRequest()
            ->shouldBeCalled()
            ->willReturn($request);
        $this->tokenStorageMock->getToken()
            ->shouldBeCalled()
            ->willReturn($this->tokenMock);
        $user = UserTest::createValidEntity();
        $this->tokenMock->getUser()
            ->shouldBeCalled()
            ->willReturn($user);

        $result = new QueryRequest($this->requestStackMock->reveal(), $this->tokenStorageMock->reveal(), $this->authorizationCheckerMock->reveal());

        $criteria = [];

        $orderCriteria = [
            'createdAt' => 'DESC',
            'fullName' => 'ASC',
        ];
        $this->assertEquals($criteria, $result->getCriteria());
        $this->assertEquals($parameters, $result->getData());
        $this->assertEquals($orderCriteria, $result->getOrder());
        $this->assertEquals(1, $result->getPage());
    }
}
