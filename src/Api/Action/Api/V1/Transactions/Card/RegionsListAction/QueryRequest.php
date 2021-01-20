<?php

namespace App\Api\Action\Api\V1\Transactions\Card\RegionsListAction;

use App\Api\Crud\Action\QueryRequest\ListQueryRequest;
use App\Security\Cabinet\MyselfInterface;
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

        if (null === $request) {
            throw new \InvalidArgumentException('Request no found');
        }
        parent::__construct($request);

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
        return ['name' => 'ASC'];
    }
}
