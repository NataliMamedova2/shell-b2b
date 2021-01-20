<?php

namespace App\Api\Action\Api\V1\Users\ListAction;

use App\Clients\Domain\User\ValueObject\Status;
use App\Clients\Infrastructure\User\Criteria\NameOrder;
use App\Security\Cabinet\Myself;
use Symfony\Component\HttpFoundation\Request;

final class QueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Myself
     */
    private $myself;

    private $allowedOrders = ['asc', 'desc'];

    private $sortPropertiesMap = [
        'createdAt' => 'createdAt',
        'status' => 'status',
        'name' => NameOrder::class,
        'role' => 'roles',
        'lastLoggedAt' => 'lastLoggedAt',
    ];

    public function __construct(Request $request, Myself $myself)
    {
        $this->request = $request;
        $this->myself = $myself;
    }

    public function getCriteria(): array
    {
        $criteria = [];
        if (null != (string) $this->request->get('status')) {
            $criteria['status_equalTo'] = Status::fromName($this->request->get('status'));
        }

        $myself = $this->myself->get();
        $company = $myself->getCompany();
        $baseCriteria = [
            'company_equalTo' => $company,
            'id_notEqualTo' => $myself->getId(),
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
