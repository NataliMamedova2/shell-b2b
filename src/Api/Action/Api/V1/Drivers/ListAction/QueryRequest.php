<?php

namespace App\Api\Action\Api\V1\Drivers\ListAction;

use App\Clients\Domain\Driver\ValueObject\Status;
use App\Clients\Infrastructure\Driver\Criteria\OrderByStatus;
use App\Clients\Infrastructure\Driver\Criteria\OrderByName;
use App\Clients\Infrastructure\Driver\Criteria\Search;
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
        'name' => OrderByName::class,
        'email' => 'email',
        'note' => 'note',
        'status' => OrderByStatus::class,
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
        if (null != (string) $this->request->get('status')) {
            $criteria['status_equalTo'] = Status::fromName($this->request->get('status'))->getValue();
        }

        if (null != (string) $this->request->get('search')) {
            $criteria[Search::class] = $this->request->get('search');
        }

        $client = $this->myself->getClient();
        $baseCriteria = [
            'client1CId_equalTo' => $client->getClient1CId(),
        ];

        return array_merge($criteria, $baseCriteria);
    }

    public function getOrder(): array
    {
        $property = 'name';
        $sortProperty = $this->request->get('sort', $property);
        $order = $this->request->get('order', 'asc');

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
