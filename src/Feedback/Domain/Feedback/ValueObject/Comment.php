<?php

declare(strict_types=1);

namespace App\Feedback\Domain\Feedback\ValueObject;

use Webmozart\Assert\Assert;

final class Comment
{
    /**
     * @var string
     */
    private $comment;

    public function __construct(string $comment)
    {
        Assert::notEmpty($comment, 'Comment can\'t be empty.');
        Assert::maxLength($comment, 500);

        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->comment;
    }

    public function __toString(): string
    {
        return \strval($this->getValue());
    }
}
