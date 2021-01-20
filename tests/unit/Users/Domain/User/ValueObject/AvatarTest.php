<?php

namespace Tests\Unit\Users\Domain\User\ValueObject;

use App\Users\Domain\User\ValueObject\Avatar;
use PHPUnit\Framework\TestCase;

class AvatarTest extends TestCase
{
    public function testCreateValid(): void
    {
        $path = '/path/to/photo';
        $fileName = 'filename.jpeg';
        $object = new Avatar($path, $fileName);

        $this->assertEquals($path, $object->getPath());
        $this->assertEquals($fileName, $object->getFileName());
    }
}
