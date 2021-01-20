<?php

namespace Tests\Unit\Clients\Domain\Company\UseCase\Update;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Company\UseCase\Update\Handler;
use App\Clients\Domain\Company\UseCase\Update\HandlerRequest;
use App\Clients\Domain\Company\ValueObject\Accounting;
use App\Clients\Domain\Company\ValueObject\Name;
use App\Clients\Domain\Company\ValueObject\PostalAddress;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Exception\EntityNotFoundException;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;

final class HandlerTest extends TestCase
{

    /**
     * @var Repository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $repositoryMock;
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
        $this->objectManagerMock = $this->prophesize(ObjectManager::class);

        $this->handler = new Handler($this->repositoryMock->reveal(), $this->objectManagerMock->reveal());
    }

    public function testHandleCompanyNotFoundReturnException(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $handlerRequest = new HandlerRequest();
        $handlerRequest->setId($string);

        $this->repositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);

        $this->handler->handle($handlerRequest);
    }

    public function testHandleCompanyFoundReturnCompanyObject(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $handlerRequest = new HandlerRequest();
        $handlerRequest->setId($string);
        $handlerRequest->accountingEmail = 'accountingEmail@email.com';
        $handlerRequest->accountingPhone = '+380976635544';
        $handlerRequest->postalAddress = 'postalAddress';

        $companyMock = $this->prophesize(Company::class);
        $this->repositoryMock->findById($handlerRequest->getId())
            ->shouldBeCalled()
            ->willReturn($companyMock);

        $clientMock = $this->prophesize(Client::class);
        $companyMock->getClient()
            ->shouldBeCalled()
            ->willReturn($clientMock);

        $clientLegalNameValue = 'company legal name value';
        $clientMock->getFullName()
            ->shouldBeCalled()
            ->willReturn($clientLegalNameValue);

        $name = $handlerRequest->name ? $handlerRequest->name : $clientLegalNameValue;
        $email = $handlerRequest->accountingEmail ? new Email($handlerRequest->accountingEmail) : null;
        $phone = new Phone($handlerRequest->accountingPhone);

        $companyMock->update(
            new Name($name),
            new Accounting($email, $phone),
            new PostalAddress($handlerRequest->postalAddress)
        )
        ->shouldBeCalled();

        $this->repositoryMock->add($companyMock->reveal());
        $this->objectManagerMock->flush()
            ->shouldBeCalled();

        $this->handler->handle($handlerRequest);
    }
}
