<?php

namespace App\Api\Action\Api\V1\Drivers\SearchAction;

use App\Clients\Domain\Driver\ValueObject\Status;
use App\Clients\Infrastructure\Driver\Criteria\OrderByName;
use App\Clients\Infrastructure\Driver\Criteria\Search;
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
        $criteria = [];
        if (null != (string) $this->request->get('q')) {
            $criteria[Search::class] = $this->request->get('q');
        }

        $client = $this->myself->getClient();
        $baseCriteria = [
            'client1CId_equalTo' => $client->getClient1CId(),
            'status_equalTo' => Status::active()->getValue(),
        ];

        return array_merge($criteria, $baseCriteria);
    }

    public function getOrder(): array
    {
        return [OrderByName::class => 'ASC'];
    }

    public function getQueryParams(): array
    {
        return [
            'limit' => (int) $this->request->get('limit', self::LIMIT),
            'offset' => (int) $this->request->get('offset', 0),
        ];
    }
}
