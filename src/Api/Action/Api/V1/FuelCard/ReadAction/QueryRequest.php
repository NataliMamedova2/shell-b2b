<?php

namespace App\Api\Action\Api\V1\FuelCard\ReadAction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request no found');
        }

        $this->request = $request;
    }

    public function getCriteria(): array
    {
        return [];
    }

    public function getOrder(): array
    {
        return [];
    }

    public function getQueryParams(): array
    {
        return [
            'id' => $this->request->attributes->get('id'),
        ];
    }
}
