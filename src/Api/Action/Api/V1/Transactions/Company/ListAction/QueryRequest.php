<?php

namespace App\Api\Action\Api\V1\Transactions\Company\ListAction;

use App\Clients\Infrastructure\Transaction\Criteria\ByClientCriteria;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var MyselfInterface
     */
    private $myself;

    private $allowedOrders = ['asc', 'desc'];

    private $sortPropertiesMap = [
        'createdAt' => 'date',
        'amount' => 'amount',
        'type' => 'type',
    ];

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
        $client = $this->myself->getClient();

        $criteria = [
            ByClientCriteria::class => $client,
        ];
        if (null != (string) $this->request->get('dateFrom')) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->request->get('dateFrom'));
            $dateErrors = \DateTime::getLastErrors();
            if (0 === $dateErrors['error_count']) {
                $date->setTime(0, 0, 0);
                $criteria['date_greaterThanOrEqualTo'] = $date;
            }
        }
        if (null != (string) $this->request->get('dateTo')) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->request->get('dateTo'));
            $dateErrors = \DateTime::getLastErrors();
            if (0 === $dateErrors['error_count']) {
                $date->setTime(23, 59, 59);
                $criteria['date_lessThanOrEqualTo'] = $date;
            }
        }
        if (null != (string) $this->request->get('cardNumber')) {
            $criteria['cardNumber_like'] = '%'.$this->request->get('cardNumber').'%';
        }

        if (null !== $this->request->get('type') && false === empty($this->request->get('type'))) {
            $type = $this->request->get('type');

            $criteria['type_equalTo'] = $type;
        }

        return $criteria;
    }

    public function getOrder(): array
    {
        $sortProperty = $this->request->get('sort');
        $order = $this->request->get('order');

        $defaultSortProperty = 'date';
        $property = isset($this->sortPropertiesMap[$sortProperty]) ? $this->sortPropertiesMap[$sortProperty] : $defaultSortProperty;

        $defaultOrder = 'desc';
        $order = (true === in_array($order, $this->allowedOrders)) ? $order : $defaultOrder;

        return [$property => $order];
    }

    public function getQueryParams(): array
    {
        return [
            'page' => (int) $this->request->get('page', 1),
        ];
    }
}
