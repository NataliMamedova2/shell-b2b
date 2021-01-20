<?php

namespace App\Clients\Action\Backend\Transaction\SuppliesListAction;

use App\Api\Crud\Action\QueryRequest\ListQueryRequest;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Infrastructure\Transaction\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequest extends ListQueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    public function __construct(RequestStack $requestStack, TransactionRepository $transactionRepository)
    {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request no found');
        }
        parent::__construct($request);
        $this->transactionRepository = $transactionRepository;
    }

    public function getCriteria(): array
    {
        $fuelTypeCodes = $this->transactionRepository->getFuelCodes();

        $criteria = ['fuelCode_in' => $fuelTypeCodes];

        $typeNames = FuelType::getNames();
        $selectedTypes = (array)$this->getRequest()->get('type');

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
