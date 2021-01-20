<?php

namespace App\Clients\Domain\Company\ValueObject;

use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\Phone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Accounting
{
    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(name="phone", type="string", length=13, nullable=true)
     */
    private $phone;

    public function __construct(?Email $email, ?Phone $phone)
    {
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function getPhone(): string
    {
        return (string) $this->phone;
    }
}
