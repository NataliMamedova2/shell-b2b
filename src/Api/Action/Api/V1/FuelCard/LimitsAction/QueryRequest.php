<?php

namespace App\Api\Action\Api\V1\FuelCard\LimitsAction;

use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Infrastructure\Fuel\Criteria\FuelLimitByType;
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

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('Request no found');
        }

        $this->request = $request;
        $this->myself = $myself;
    }

    public function getCriteria(): array
    {
        $type = $this->request->get('type', FuelType::fuel()->getName());

        return [
            FuelLimitByType::class => FuelType::fromName($type),
        ];
    }

    public function getOrder(): array
    {
        return [];
    }

    public function getQueryParams(): array
    {
        return [
            'cardId' => $this->request->get('id'),
        ];
    }
}
