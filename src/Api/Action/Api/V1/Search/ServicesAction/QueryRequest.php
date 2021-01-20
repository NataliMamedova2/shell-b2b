<?php

namespace App\Api\Action\Api\V1\Search\ServicesAction;

use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Infrastructure\Fuel\Criteria\Search;
use Symfony\Component\HttpFoundation\Request;

final class QueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    private const LIMIT = 100;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getCriteria(): array
    {
        $criteria = [];
        if (null != (string) $this->request->get('q')) {
            $criteria[Search::class] = (string) $this->request->get('q');
        }

        $baseCriteria = [
            'fuelType_equalTo' => FuelType::service()->getValue(),
            'purseCode_greaterThan' => 0,
        ];

        return array_merge($criteria, $baseCriteria);
    }

    public function getOrder(): array
    {
        return ['fuelName' => 'ASC'];
    }

    public function getQueryParams(): array
    {
        return [
            'limit' => (int) $this->request->get('limit', self::LIMIT),
            'offset' => (int) $this->request->get('offset', 0),
        ];
    }
}
