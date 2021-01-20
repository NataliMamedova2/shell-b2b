<?php

namespace App\Api\Action\Api\V1\Documents\ListAction;

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

    private $allowedOrders = ['asc', 'desc'];

    private $sortPropertiesMap = [
        'createdAt' => 'createdAt',
    ];

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
        $criteria = [];

        $client = $this->myself->getClient();
        $baseCriteria = [
            'client1CId_equalTo' => $client->getClient1CId(),
        ];

        return array_merge($criteria, $baseCriteria);
    }

    public function getOrder(): array
    {
        $sortProperty = $this->request->get('sort', 'createdAt');
        $order = $this->request->get('order', 'desc');

        $property = 'createdAt';
        if (false === in_array($order, $this->allowedOrders)) {
            $order = 'desc';
        }

        if (isset($this->sortPropertiesMap[$sortProperty])) {
            $property = $this->sortPropertiesMap[$sortProperty];
        }

        return [$property => $order];
    }

    public function getQueryParams(): array
    {
        return [
            'page' => (int) $this->request->get('page', 1),
        ];
    }
}
