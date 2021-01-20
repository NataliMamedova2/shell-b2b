<?php

namespace App\Api\Action\Api\V1\Drivers\ReadAction;

use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var MyselfInterface
     */
    private $myself;

    public function __construct(RequestStack $requestStack, MyselfInterface $myself)
    {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request no found');
        }

        $this->request = $request;
        $this->myself = $myself;
    }

    public function getCriteria(): array
    {
        $client = $this->myself->getClient();

        return [
            'id_equalTo' => $this->request->get('id'),
            'client1CId_equalTo' => $client->getClient1CId(),
        ];
    }

    public function getOrder(): array
    {
        return [];
    }

    public function getQueryParams(): array
    {
        return [];
    }
}
