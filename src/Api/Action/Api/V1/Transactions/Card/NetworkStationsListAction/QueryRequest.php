<?php

namespace App\Api\Action\Api\V1\Transactions\Card\NetworkStationsListAction;

use App\Api\Crud\Action\QueryRequest\ListQueryRequest;
use App\Clients\Infrastructure\Transaction\Criteria\NetworkStation\SearchNameCriteria;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequest extends ListQueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    /**
     * @var MyselfInterface
     */
    private $myself;

    public function __construct(RequestStack $requestStack, MyselfInterface $myself)
    {
        $request = $requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('Request no found');
        }
        parent::__construct($request);

        $this->myself = $myself;
    }

    public function getCriteria(): array
    {
        $client = $this->myself->getClient();
        $criteria = [
            'client1CId_equalTo' => $client->getClient1CId(),
        ];
        if (null != (string) $this->getRequest()->get('q')) {
            $criteria[SearchNameCriteria::class] = (string) $this->getRequest()->get('q');
        }

        return $criteria;
    }

    public function getOrder(): array
    {
        return ['name' => 'ASC'];
    }
}
