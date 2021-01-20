<?php

namespace Tests\Unit\Clients\Domain\Driver\ValueObject;

use App\Clients\Domain\Driver\ValueObject\Name;
use PHPUnit\Framework\TestCase;

final class NameTest extends TestCase
{

    public function testConstructValidDataReturnObject(): void
    {
        $firstName = 'firstName';
        $middleName = 'middleName';
        $lastName = 'lastName';

        $result = new Name($firstName, $middleName, $lastName);

        $this->assertEquals($firstName, $result->getFirstName());
        $this->assertEquals($middleName, $result->getMiddleName());
        $this->assertEquals($lastName, $result->getLastName());
    }

    public function testConstructEmptyMiddleNameReturnObject(): void
    {
        $firstName = 'firstName';
        $middleName = '';
        $lastName = 'lastName';

        $result = new Name($firstName, $middleName, $lastName);

        $this->assertEquals($firstName, $result->getFirstName());
        $this->assertEquals($middleName, $result->getMiddleName());
        $this->assertEquals($lastName, $result->getLastName());
    }

    public function testConstructNullableMiddleNameReturnObject(): void
    {
        $firstName = 'firstName';
        $middleName = null;
        $lastName = 'lastName';

        $result = new Name($firstName, $middleName, $lastName);

        $this->assertEquals($firstName, $result->getFirstName());
        //$this->assertEquals($middleName, $result->getMiddleName());
        $this->assertEquals($lastName, $result->getLastName());
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param $firstName
     * @param $middleName
     * @param $lastName
     */
    public function testConstructInvalidDataReturnException($firstName, $middleName, $lastName): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Name($firstName, $middleName, $lastName);
    }

    public function invalidDataProvider()
    {
        return [
            'empty firstName' => ['', 'middleName', 'lastName'],
            'empty lastName' => ['firstName', 'middleName', ''],
            'firstName greater than 30' => ['longFirstNamelongFirstNamelong1', 'middleName', 'lastName'],
            //'middleName greater than 30' => ['firstName', 'longMiddleNamelongMiddleNamelon', 'lastName'],
            'lastName greater than 30' => ['firstName', 'middleName', 'longLastNamelongLastNamelong111'],
        ];
    }
}
