<?php

namespace App\Api\Action\Api\V1\Discounts;

use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    private const LIMIT = 100;

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
            'client1CId_equalTo' => $client->getClient1CId(),
        ];
    }

    public function getOrder(): array
    {
        return ['operationDate' => 'DESC'];
    }

    public function getQueryParams(): array
    {
        return [
            'limit' => (int) $this->request->get('limit', self::LIMIT),
            'offset' => (int) $this->request->get('offset', 0),
        ];
    }
}
