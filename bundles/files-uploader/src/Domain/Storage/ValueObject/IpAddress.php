<?php

declare(strict_types=1);

namespace FilesUploader\Domain\Storage\ValueObject;

final class IpAddress
{
    /**
     * @var string
     */
    private $value;

    /**
     * IpAddress constructor.
     *
     * @param string $value the ip address
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @param $string
     *
     * @return IpAddress
     */
    public static function fromString($string): self
    {
        return new self($string);
    }

    /**
     * @param self $other
     *
     * @return bool
     */
    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
