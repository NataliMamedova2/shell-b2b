<?php

namespace Tests\Unit\Api\Action\Api\V1\Company;

use App\Api\Action\Api\V1\Company\ProfileAction;
use App\Api\Crud\Interfaces\Response;
use App\Api\Resource\Company;
use App\Clients\Domain\User\User;
use App\Security\Cabinet\MyselfInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\Unit\Clients\Domain\Company\CompanyTest;

final class ProfileActionTest extends TestCase
{
    /**
     * @var MyselfInterface|ObjectProphecy
     */
    private $myselfMock;
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
        $this->jsonResponseMock = $this->prophesize(Response::class);

        $this->action = new ProfileAction($this->myselfMock->reveal(), $this->jsonResponseMock->reveal());
    }

    public function testInvokeReturnSymfonyResponse(): void
    {
        $userMock = $this->prophesize(User::class);
        $this->myselfMock->get()
            ->shouldBeCalled()
            ->willReturn($userMock);

        $company = CompanyTest::createValidEntity();
        $userMock->getCompany()
            ->shouldBeCalled()
            ->willReturn($company);

        $companyResource = new Company();
        $companyResource->prepare($company);

        $data = [
            'name' => $company->getName(),
            'legalName' => $company->getClient()->getFullName(),
            'accountingEmail' => $company->getAccounting()->getEmail(),
            'accountingPhone' => $company->getAccounting()->getPhone(),
            'directorEmail' => $company->getEmail(),
            'postalAddress' => $company->getPostalAddress(),
        ];

        $responseMock = \Symfony\Component\HttpFoundation\Response::create(json_encode($data));
        $this->jsonResponseMock->createSuccessResponse($companyResource)
            ->shouldBeCalled()
            ->willReturn($responseMock);

        $result = $this->action->__invoke();

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $result);
        $this->assertEquals(json_encode($data), $result->getContent());
    }
}
