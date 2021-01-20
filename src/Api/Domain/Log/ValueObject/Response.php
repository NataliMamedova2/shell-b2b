<?php

declare(strict_types=1);

namespace App\Api\Domain\Log\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Response
{

    /**
     * @var int
     * @ORM\Column(type="string", nullable=false)
     */
    private $code;

    /**
     * @var array
     *
     * @ORM\Column(type="json", options={"jsonb": true})
     */
    private $headers;

    /**
     * @var array
     *
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private $body;

    public function __construct(int $code, array $headers, ?array $body)
    {
        $this->code = $code;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return (int) $this->code;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array|null
     */
    public function getBody(): ?array
    {
        return $this->body;
    }
}
