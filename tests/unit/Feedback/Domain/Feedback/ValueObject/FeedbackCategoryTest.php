<?php

namespace Tests\Unit\Feedback\Domain\Feedback\ValueObject;

use App\Feedback\Domain\Feedback\ValueObject\FeedbackCategory;
use PHPUnit\Framework\TestCase;

final class FeedbackCategoryTest extends TestCase
{
    public function testShouldNotCreateWithEmptyValueReturnArgumentCountException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        new FeedbackCategory();
    }

    public function testCreateNotValidValueReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = 2;

        new FeedbackCategory($value);
    }

    /**
     * @param $value
     * @dataProvider validDataProvider
     */
    public function testCreateValidValueReturnObject($value): void
    {
        $result = new FeedbackCategory($value);

        $this->assertEquals($value, $result->getValue());
        $this->assertEquals($value, $result->__toString());
        $this->assertEquals($value, (string) $result);
    }

    public function validDataProvider(): array
    {
        return [
            'general_question' => ['general-question'],
            'financial_issue' => ['financial-issue'],
            'new_card_order' => ['new-card-order'],
            'complaints' => ['complaints'],
        ];
    }
}
