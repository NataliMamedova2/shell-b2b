<?php

namespace App\Clients\Domain\Document\ValueObject;

use Webmozart\Assert\Assert;

final class Type
{
    private const INVOICE = 0;
    private const ACT_CHECKING = 1;
    private const APPENDIX_PETROLEUM_PRODUCTS = 2;
    private const CARD_INVOICE = 3;
    private const ACCEPTANCE_TRANSFER_ACT = 4;

    private static $names = [
        self::INVOICE => 'invoice',
        self::ACT_CHECKING => 'act-checking',
        self::APPENDIX_PETROLEUM_PRODUCTS => 'appendix-petroleum-products',
        self::CARD_INVOICE => 'card-invoice',
        self::ACCEPTANCE_TRANSFER_ACT => 'acceptance-transfer-act',
    ];

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        Assert::oneOf($value, [
            self::INVOICE,
            self::ACT_CHECKING,
            self::APPENDIX_PETROLEUM_PRODUCTS,
            self::CARD_INVOICE,
            self::ACCEPTANCE_TRANSFER_ACT,
        ]);

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public static function getNames(): array
    {
        return self::$names;
    }

    public function getName(): string
    {
        return self::$names[$this->value];
    }

    public static function fromName(string $name): self
    {
        $names = array_flip(self::$names);

        return new self($names[$name]);
    }

    public static function invoice(): self
    {
        return new self(self::INVOICE);
    }

    public static function actChecking(): self
    {
        return new self(self::ACT_CHECKING);
    }

    public static function appendixPetroleumProducts(): self
    {
        return new self(self::APPENDIX_PETROLEUM_PRODUCTS);
    }

    public static function cardInvoice(): self
    {
        return new self(self::CARD_INVOICE);
    }

    public static function acceptanceTransferAct(): self
    {
        return new self(self::ACCEPTANCE_TRANSFER_ACT);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
