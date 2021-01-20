<?php

namespace App\Export\Domain\Export\ValueObject;

use Webmozart\Assert\Assert;

final class Type
{
    private const TYPE_1C = '1c';
    private const TYPE_PC = 'pc';

    /**
     * @var string
     */
    private $value;

    private function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::TYPE_1C,
            self::TYPE_PC,
        ]);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function type1C(): self
    {
        return new self(self::TYPE_1C);
    }

    public static function typePC(): self
    {
        return new self(self::TYPE_PC);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
