<?php

declare(strict_types=1);

namespace App\Api\Domain\Log;

use Doctrine\ORM\Mapping as ORM;
use App\Api\Domain\Log\ValueObject\LogId;
use App\Api\Domain\Log\ValueObject\Resource as ApiResource;
use App\Api\Domain\Log\ValueObject\Request;
use App\Api\Domain\Log\ValueObject\Response;
use App\Api\Domain\Log\ValueObject\IPAddress;
use DateTimeInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="api_logs")
 */
class Log
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $resource;

    /**
     * @var Request
     *
     * @ORM\Embedded(class="App\Api\Domain\Log\ValueObject\Request")
     */
    private $request;

    /**
     * @var Response
     *
     * @ORM\Embedded(class="App\Api\Domain\Log\ValueObject\Response")
     */
    private $response;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $IPAddress;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    private function __construct(
        LogId $id,
        ApiResource $resource,
        Request $request,
        Response $response,
        IPAddress $IPAddress
    ) {
        $this->id = $id->getId();
        $this->resource = $resource->getValue();
        $this->request = $request;
        $this->response = $response;
        $this->IPAddress = $IPAddress->getValue();
    }

    public static function create(ApiResource $resource, Request $request, Response $response, IPAddress $IPAddress): self
    {
        $self = new self(LogId::next(), $resource, $request, $response, $IPAddress);

        $self->createdAt = new \DateTimeImmutable();

        return $self;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getIPAddress(): string
    {
        return $this->IPAddress;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
