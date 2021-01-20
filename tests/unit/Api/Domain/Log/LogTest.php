<?php

namespace Tests\Unit\Api\Domain\Log;

use App\Api\Domain\Log\Log;
use App\Api\Domain\Log\ValueObject\IPAddress;
use App\Api\Domain\Log\ValueObject\Request;
use App\Api\Domain\Log\ValueObject\Resource;
use App\Api\Domain\Log\ValueObject\Response;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{

    public function testCreate(): void
    {
        $resource = new Resource('/api/url');
        $request = new Request('POST', ['Content-Type' => 'application/json'], ['key' => 'value']);
        $response = new Response('200', ['Content-Type' => 'application/json'], ['key' => 'value']);
        $IPAddress = new IPAddress('127.0.0.1');

        $entity = Log::create(
            $resource,
            $request,
            $response,
            $IPAddress
        );

        $this->assertEquals($resource, $entity->getResource());
        $this->assertEquals($request, $entity->getRequest());
        $this->assertEquals($response, $entity->getResponse());
        $this->assertEquals($IPAddress, $entity->getIPAddress());
    }
}
