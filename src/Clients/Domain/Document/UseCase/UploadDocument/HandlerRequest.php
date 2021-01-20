<?php

namespace App\Clients\Domain\Document\UseCase\UploadDocument;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Document\ValueObject\File;
use App\Clients\Domain\Document\ValueObject\Type;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Type
     */
    private $type;

    /**
     * @var File
     */
    private $file;

    public function __construct(Client $client, File $file, Type $type)
    {
        $this->client = $client;
        $this->file = $file;
        $this->type = $type;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getFile(): File
    {
        return $this->file;
    }
}
