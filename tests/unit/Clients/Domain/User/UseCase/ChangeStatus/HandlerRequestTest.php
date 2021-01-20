<?php

namespace Tests\Unit\Clients\Domain\User\UseCase\ChangeStatus;

use App\Clients\Domain\User\UseCase\ChangeStatus\HandlerRequest;
use PHPUnit\Framework\TestCase;

final class HandlerRequestTest extends TestCase
{
    public function testSetIdEmptyValueReturnException(): void
    {
        $handlerRequest = new HandlerRequest();

        $this->expectException(\InvalidArgumentException::class);

        $handlerRequest->setId('');
    }

    public function testSetIdNotEmptyValueReturnException(): void
    {
        $handlerRequest = new HandlerRequest();

        $id = '209b82cb-6f17-4020-ace4-54f6bbecd388';
        $handlerRequest->setId($id);

        $this->assertEquals($id, $handlerRequest->getId());
    }
}
