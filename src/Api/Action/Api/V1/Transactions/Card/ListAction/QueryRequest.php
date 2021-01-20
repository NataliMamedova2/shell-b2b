<?php

namespace App\Api\Action\Api\V1\Transactions\Card\ListAction;

use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use App\Clients\Infrastructure\Fuel\Criteria\FuelNameOrder;
use App\Clients\Infrastructure\Transaction\Criteria\SupplyTypeCriteria;
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
        'createdAt' => 'postDate',
        'cardNumber' => 'cardNumber',
        'fuelName' => FuelNameOrder::class,
        'volume' => 'fuelQuantity',
        'amount' => 'debit',
        'price' => 'stellaPrice',
        'networkStation' => 'azsName',
        'status' => 'type',
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
            'client1CId_equalTo' => $client->getClient1CId(),
        ];

        if (null != (string) $this->request->get('dateFrom')) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->request->get('dateFrom'));
            $dateErrors = \DateTime::getLastErrors();
            if (0 === $dateErrors['error_count']) {
                $date->setTime(0, 0, 0);
                $criteria['postDate_greaterThanOrEqualTo'] = $date;
            }
        }
        if (null != (string) $this->request->get('dateTo')) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->request->get('dateTo'));
            $dateErrors = \DateTime::getLastErrors();
            if (0 === $dateErrors['error_count']) {
                $date->setTime(23, 59, 59);
                $criteria['postDate_lessThanOrEqualTo'] = $date;
            }
        }
        if (null != (string) $this->request->get('cardNumber')) {
            $criteria['cardNumber_like'] = '%'.$this->request->get('cardNumber').'%';
        }
        $regions = (array) $this->request->get('regions');
        if (!empty($regions)) {
            $regionCodes = array_diff($regions, ['']);
            if (!empty($regionCodes)) {
                $criteria['regionCode_in'] = $regionCodes;
            }
        }
        $supplyTypes = (array) $this->request->get('supplyTypes');
        if (!empty($supplyTypes)) {
            $typeNames = FuelType::getNames();
            $typesValues = [];
            foreach ($supplyTypes as $selectedType) {
                if (in_array($selectedType, $typeNames)) {
                    $typesValues[] = FuelType::fromName($selectedType)->getValue();
                }
            }
            if (!empty($typesValues)) {
                $criteria[SupplyTypeCriteria::class] = $typesValues;
            }
        }
        $supplies = (array) $this->request->get('supplies');
        if (!empty($supplies)) {
            $fuelCodes = array_diff($supplies, ['']);
            if (!empty($fuelCodes)) {
                $criteria['fuelCode_in'] = $fuelCodes;
            }
        }
        $networkStations = (array) $this->request->get('networkStations');
        if (!empty($networkStations)) {
            $azsCodes = array_diff($networkStations, ['']);
            if (!empty($azsCodes)) {
                $criteria['azsCode_in'] = $azsCodes;
            }
        }
        $status = $this->request->get('status');
        $statusNames = Type::getNames();
        if (in_array($status, $statusNames)) {
            $criteria['type_equalTo'] = Type::fromName($status)->getValue();
        }

        return $criteria;
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
            'supplies' => (array) $this->request->get('supplies', []),
            'regions' => (array) $this->request->get('regions', []),
            'networkStations' => (array) $this->request->get('networkStations', []),
        ];
    }
}
