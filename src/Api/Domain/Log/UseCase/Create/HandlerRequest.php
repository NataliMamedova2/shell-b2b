<?php

declare(strict_types=1);

namespace App\Api\Domain\Log\UseCase\Create;

use App\Api\Domain\Log\ValueObject\IPAddress;
use App\Api\Domain\Log\ValueObject\Request;
use App\Api\Domain\Log\ValueObject\Resource as ApiResource;
use App\Api\Domain\Log\ValueObject\Response;

final class HandlerRequest
{
    /**
     * @var ApiResource
     */
    public $resource;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var IPAddress
     */
    public $IPAddress;
}
