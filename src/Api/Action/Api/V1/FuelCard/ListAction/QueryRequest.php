<?php

namespace App\Api\Action\Api\V1\FuelCard\ListAction;

use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Infrastructure\FuelCard\Criteria\CardNumberLike;
use App\Clients\Infrastructure\FuelCard\Criteria\OrderByStatus;
use App\Clients\Infrastructure\FuelCard\Criteria\QueryStringCriteria;
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
        'cardNumber' => 'cardNumber',
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
            $criteria['status_equalTo'] = CardStatus::fromName($this->request->get('status'));
        }

        if (null != (string) $this->request->get('cardNumber')) {
            $criteria[CardNumberLike::class] = $this->request->get('cardNumber');
        }

        if (null != (string) $this->request->get('queryString')) {
            $criteria[QueryStringCriteria::class] = $this->request->get('queryString');
        }

        $client = $this->myself->getClient();
        $baseCriteria = [
            'client1CId_equalTo' => $client->getClient1CId(),
        ];

        return array_merge($criteria, $baseCriteria);
    }

    public function getOrder(): array
    {
        $property = 'cardNumber';

        $sortProperty = $this->request->get('sort', $property);
        $order = $this->request->get('order', 'desc');

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
