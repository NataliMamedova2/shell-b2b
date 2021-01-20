<?php

namespace Tests\Unit\Clients\Domain\User\ValueObject;

use App\Clients\Domain\User\ValueObject\Name;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{

    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\TypeError::class);

        new Name(null, null, null);
    }

    public function testCreateValidValueReturnObject(): void
    {
        $firstName = 'John';
        $middleName = 'Mike';
        $lastName = 'Dou';

        $result = new Name($firstName, $middleName, $lastName);

        $fullName = 'John Mike Dou';

        $this->assertEquals($firstName, $result->getFirstName());
        $this->assertEquals($middleName, $result->getMiddleName());
        $this->assertEquals($lastName, $result->getLastName());
        $this->assertEquals($fullName, $result->__toString());
        $this->assertEquals($fullName, (string) $result);
    }

    public function testCreateValidValueEmptyMiddleReturnObject(): void
    {
        $firstName = 'John';
        $middleName = '';
        $lastName = 'Dou';

        $result = new Name($firstName, $middleName, $lastName);

        $fullName = 'John Dou';

        $this->assertEquals($firstName, $result->getFirstName());
        $this->assertEquals($middleName, $result->getMiddleName());
        $this->assertEquals($lastName, $result->getLastName());
        $this->assertEquals($fullName, $result->__toString());
        $this->assertEquals($fullName, (string) $result);
    }

    public function testCreateValidValueNullMiddleReturnObject(): void
    {
        $firstName = 'John';
        $middleName = null;
        $lastName = 'Dou';

        $result = new Name($firstName, $middleName, $lastName);

        $fullName = 'John Dou';

        $this->assertEquals($firstName, $result->getFirstName());
        $this->assertEquals($middleName, $result->getMiddleName());
        $this->assertEquals($lastName, $result->getLastName());
        $this->assertEquals($fullName, $result->__toString());
        $this->assertEquals($fullName, (string) $result);
    }

    public function testCreateEmptyFirstReturnObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $firstName = '';
        $middleName = null;
        $lastName = 'Dou';

        new Name($firstName, $middleName, $lastName);
    }

    public function testCreateEmptyLastReturnObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $firstName = 'John';
        $middleName = null;
        $lastName = '';

        new Name($firstName, $middleName, $lastName);
    }
}
