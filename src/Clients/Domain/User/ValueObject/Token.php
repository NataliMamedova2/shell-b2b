<?php

namespace App\Clients\Domain\User\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
final class Token
{
    private const EXPIRE_PERIOD = '+7 days';

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expire;

    public function __construct(string $token)
    {
        Assert::notEmpty($token);

        $this->token = $token;
        $this->expire = new \DateTimeImmutable(self::EXPIRE_PERIOD);
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expire <= $date;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
