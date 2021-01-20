<?php

namespace Tests\Unit\Traits;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;

trait UuidMock
{
    /**
     * Sets the expected responses from `Uuid::uuid4()`.
     * If you're using this method, make sure to call `clearUuid()` in tearDown.
     *
     * @param string $value representations of Uuid
     */
    protected function setUuid4Mock(string $value): void
    {
        $uuid = Uuid::fromString($value);

        $factory = $this->createMock(UuidFactoryInterface::class);
        $factory
            ->method('uuid4')
            ->willReturn($uuid);

        Uuid::setFactory($factory);
    }

    protected function clearUuid(): void
    {
        Uuid::setFactory(new UuidFactory());
    }
}
