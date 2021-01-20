<?php

namespace Tests\Unit\Security\Cabinet;

use App\Security\Cabinet\Myself;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tests\Unit\Clients\Domain\User\UserTest;

final class MyselfTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|TokenStorageInterface
     */
    private $tokenStorageMock;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|TokenInterface
     */
    private $tokenMock;

    protected function setUp(): void
    {
        $this->tokenStorageMock = $this->prophesize(TokenStorageInterface::class);
        $this->tokenMock = $this->prophesize(TokenInterface::class);
    }

    public function testTokenIsEmptyThrowException(): void
    {
        $this->expectException(UnauthorizedHttpException::class);

        $this->tokenStorageMock->getToken()
            ->shouldBeCalled()
            ->willReturn(null);

        new Myself($this->tokenStorageMock->reveal());
    }

    public function testGetMyselfUserIsEmptyThrowException(): void
    {
        $this->tokenStorageMock->getToken()
            ->shouldBeCalled()
            ->willReturn($this->tokenMock);
        $this->tokenMock->getUser()
            ->shouldBeCalled()
            ->willReturn(null);

        $myself = new Myself($this->tokenStorageMock->reveal());

        $this->expectException(UnauthorizedHttpException::class);
        $myself->get();
    }

    public function testGetMyselfReturnUser(): void
    {
        $this->tokenStorageMock->getToken()
            ->shouldBeCalled()
            ->willReturn($this->tokenMock);

        $user = UserTest::createValidEntity();
        $this->tokenMock->getUser()
            ->shouldBeCalled()
            ->willReturn($user);

        $myself = new Myself($this->tokenStorageMock->reveal());

        $result = $myself->get();

        $this->assertEquals($user, $result);
    }

    public function testGetCompanyReturnCompany(): void
    {
        $this->tokenStorageMock->getToken()
            ->shouldBeCalled()
            ->willReturn($this->tokenMock);

        $user = UserTest::createValidEntity();
        $this->tokenMock->getUser()
            ->shouldBeCalled()
            ->willReturn($user);

        $myself = new Myself($this->tokenStorageMock->reveal());

        $result = $myself->getCompany();

        $this->assertEquals($user->getCompany(), $result);
    }

    public function testGetClientReturnClient(): void
    {
        $this->tokenStorageMock->getToken()
            ->shouldBeCalled()
            ->willReturn($this->tokenMock);

        $user = UserTest::createValidEntity();
        $this->tokenMock->getUser()
            ->shouldBeCalled()
            ->willReturn($user);

        $myself = new Myself($this->tokenStorageMock->reveal());

        $result = $myself->getClient();

        $this->assertEquals($user->getCompany()->getClient(), $result);
    }
}
