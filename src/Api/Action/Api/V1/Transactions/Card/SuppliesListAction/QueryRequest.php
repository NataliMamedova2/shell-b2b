<?php

namespace App\Api\Action\Api\V1\Transactions\Card\SuppliesListAction;

use App\Api\Crud\Action\QueryRequest\ListQueryRequest;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Infrastructure\Transaction\Repository\Repository;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequest extends ListQueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    /**
     * @var MyselfInterface
     */
    private $myself;

    /**
     * @var Repository
     */
    private $transactionRepository;

    public function __construct(RequestStack $requestStack, MyselfInterface $myself, Repository $transactionRepository)
    {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request no found');
        }
        parent::__construct($request);

        $this->myself = $myself;
        $this->transactionRepository = $transactionRepository;
    }

    public function getCriteria(): array
    {
        $client = $this->myself->getClient();
        $fuelTypeCodes = $this->transactionRepository->getClientFuelCodes($client);

        $criteria = ['fuelCode_in' => $fuelTypeCodes];

        $typeNames = FuelType::getNames();
        $selectedTypes = (array) $this->getRequest()->get('type');

        $typesValues = [];
        foreach ($selectedTypes as $selectedType) {
            if (in_array($selectedType, $typeNames)) {
                $typesValues[] = FuelType::fromName($selectedType)->getValue();
            }
        }
        if (!empty($typesValues)) {
            $criteria['fuelType_in'] = $typesValues;
        }

        return $criteria;
    }

    public function getOrder(): array
    {
        return ['fuelName' => 'ASC'];
    }
}
