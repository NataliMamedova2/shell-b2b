<?php

declare(strict_types=1);

namespace App\Api\Domain\Log\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Request
{

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $method;

    /**
     * @var array
     *
     * @ORM\Column(type="json", options={"jsonb": true})
     */
    private $headers;

    /**
     * @var array
     *
     * @ORM\Column(type="json", options={"jsonb": true})
     */
    private $body;

    public function __construct(string $method, array $headers, ?array $body)
    {
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
