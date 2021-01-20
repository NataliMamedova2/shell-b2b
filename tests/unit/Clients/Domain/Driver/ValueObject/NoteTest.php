<?php

namespace Tests\Unit\Clients\Domain\Driver\ValueObject;

use App\Clients\Domain\Driver\ValueObject\Note;
use PHPUnit\Framework\TestCase;

final class NoteTest extends TestCase
{
    public function testConstructValidDataReturnObject(): void
    {
        $value = 'Valid note text';
        $result = new Note($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
    }

    /**
     * @param $value
     * @dataProvider invalidDataProvider
     */
    public function testCreateInvalidValueReturnObject($value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Note($value);
    }

    public function invalidDataProvider()
    {
        return [
            'empty' => [''],
            'greater than 250' => ['Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. 12345'],
        ];
    }
}
