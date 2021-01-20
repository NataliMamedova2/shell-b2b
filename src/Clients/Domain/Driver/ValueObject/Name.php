<?php

declare(strict_types=1);

namespace App\Clients\Domain\Driver\ValueObject;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Name
{
    /**
     * @ORM\Column(type="string", length=30, nullable=false)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $middleName;

    /**
     * @ORM\Column(type="string", length=30, nullable=false)
     */
    private $lastName;

    public function __construct(string $firstName, ?string $middleName, string $lastName)
    {
        Assert::notEmpty($firstName, 'First name can\'t be empty.');
        Assert::notEmpty($lastName, 'Last name can\'t be empty.');
        Assert::maxLength($firstName, 30);
        //Assert::maxLength($middleName, 30);
        Assert::maxLength($lastName, 30);

        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Returns the full name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        $array = [$this->lastName, $this->firstName, $this->middleName];
        $array = array_diff($array, ['', null]);

        return implode(' ', $array);
    }
}
