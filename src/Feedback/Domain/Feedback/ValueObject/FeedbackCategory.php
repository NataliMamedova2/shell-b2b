<?php

declare(strict_types=1);

namespace App\Feedback\Domain\Feedback\ValueObject;

use Webmozart\Assert\Assert;

final class FeedbackCategory
{
    private const GENERAL = 'general-question';
    private const FINANCIAL = 'financial-issue';
    private const NEW_CARD = 'new-card-order';
    private const COMPLAINTS = 'complaints';

    private static $names = [
        self::GENERAL,
        self::FINANCIAL,
        self::NEW_CARD,
        self::COMPLAINTS,
    ];

    private $managersEmails = [
        self::GENERAL => 'GXUKRAHCardsCSC@shell.com',
        self::FINANCIAL => 'V.Ryazanova@shell.com',
        self::NEW_CARD => 'GXUKRAHCardsCSC@shell.com',
        self::COMPLAINTS => 'm.vozhakov@aurocraft.com',
    ];

    /**
     * @var string
     */
    private $category;

    public function __construct(string $category)
    {
        Assert::oneOf($category, [
            self::GENERAL,
            self::FINANCIAL,
            self::NEW_CARD,
            self::COMPLAINTS,
        ]);

        $this->category = $category;
    }

    public static function getNames(): array
    {
        return self::$names;
    }

    public function getValue(): string
    {
        return $this->category;
    }

    public function __toString(): string
    {
        return \strval($this->getValue());
    }

    public function getManagerEmail()
    {
        return $this->managersEmails[$this->category];
    }
}
