<?php

namespace App\Api\Action\Api\V1\FuelCard\ListAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Security\Cabinet\Myself;
use Infrastructure\Interfaces\Paginator\Paginator;
use Infrastructure\Interfaces\Repository\Repository;

final class ListService implements QueryHandler
{
    private const LIMIT = 20;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var Repository
     */
    private $fuelCardRepository;

    /**
     * @var Repository
     */
    private $fuelCardLimitRepository;

    /**
     * @var Myself
     */
    private $myself;

    public function __construct(
        Paginator $paginator,
        Repository $fuelCardRepository,
        Repository $fuelCardLimitRepository,
        Myself $myself
    ) {
        $this->paginator = $paginator;
        $this->fuelCardRepository = $fuelCardRepository;
        $this->fuelCardLimitRepository = $fuelCardLimitRepository;
        $this->myself = $myself;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $params = $queryRequest->getQueryParams();

        $page = isset($params['page']) ? (int) $params['page'] : 1;

        $result = $this->paginator->paginate(
            $queryRequest->getCriteria(),
            $queryRequest->getOrder(),
            $page,
            self::LIMIT
        );

        $client = $this->myself->getClient();
        $activeCount = $this->fuelCardRepository->count([
            'client1CId_equalTo' => $client->getClient1CId(),
            'status_equalTo' => CardStatus::active()->getValue(),
        ]);

        /** @var FuelLimit[] $limitsOnModeration */
        $limitsOnModeration = $this->fuelCardLimitRepository->findMany([
            'client1CId_equalTo' => $client->getClient1CId(),
            ExportStatusCriteria::class => ExportStatus::cantBeEditedStatuses(),
        ]);
        $limitsCardNumbersOnModeration = [];
        foreach ($limitsOnModeration as $limit) {
            if (false === in_array($limit->getCardNumber(), $limitsCardNumbersOnModeration)) {
                $limitsCardNumbersOnModeration[] = $limit->getCardNumber();
            }
        }

        return [
            'paginator' => $result,
            'activeCount' => $activeCount,
            'limitsCardNumbersOnModeration' => $limitsCardNumbersOnModeration,
        ];
    }
}
