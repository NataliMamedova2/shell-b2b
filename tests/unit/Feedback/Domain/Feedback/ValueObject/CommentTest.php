<?php

namespace Tests\Unit\Feedback\Domain\Feedback\ValueObject;

use App\Feedback\Domain\Feedback\ValueObject\Comment;
use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new Comment();
    }

    /**
     * @param $value
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new Comment($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider()
    {
        return [
            ['test comment']
        ];
    }
}
