<?php

declare(strict_types=1);

namespace App\Clients\Domain\Client\ValueObject;

use Webmozart\Assert\Assert;

final class ContractNumber
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
