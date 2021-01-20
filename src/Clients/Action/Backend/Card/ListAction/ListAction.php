<?php

namespace App\Clients\Action\Backend\Card\ListAction;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Infrastructure\Client\Criteria\IndexByClient1CId;
use CrudBundle\Action\Response;
use CrudBundle\Interfaces\ListQueryRequest;
use CrudBundle\Service\TargetRoute;
use Infrastructure\Interfaces\Paginator\Paginator;
use Infrastructure\Interfaces\Repository\Repository;

final class ListAction
{
    /**
     * @var Paginator
     */
    private $cardPaginator;

    /**
     * @var Repository
     */
    private $clientRepository;

    /**
     * @var Repository
     */
    private $fuelCardLimitRepository;

    /**
     * @var TargetRoute
     */
    private $targetRoute;

    public function __construct(
        Paginator $cardPaginator,
        Repository $clientRepository,
        Repository $fuelCardLimitRepository,
        TargetRoute $targetRoute
    ) {
        $this->cardPaginator = $cardPaginator;
        $this->clientRepository = $clientRepository;
        $this->fuelCardLimitRepository = $fuelCardLimitRepository;
        $this->targetRoute = $targetRoute;
    }

    public function __invoke(ListQueryRequest $listQueryRequest): Response
    {
        /** @var Card[] $collection */
        $collection = $this->cardPaginator->paginate(
            $listQueryRequest->getCriteria(),
            $listQueryRequest->getOrder(),
            $listQueryRequest->getPage(),
            $listQueryRequest->getLimit()
        );

        $client1CId = [];
        foreach ($collection as $entity) {
            $client1CId[$entity->getClient1CId()] = $entity->getClient1CId();
        }

        $clients = $this->clientRepository->findMany([
            'client1CId_in' => array_values($client1CId),
            IndexByClient1CId::class => true,
        ]);

        /** @var FuelLimit[] $limitsOnModeration */
        $limitsOnModeration = $this->fuelCardLimitRepository->findMany([
            'client1CId_in' => array_values($client1CId),
            ExportStatusCriteria::class => ExportStatus::cantBeEditedStatuses(),
        ]);
        $limitsCardNumbersOnModeration = [];
        foreach ($limitsOnModeration as $limit) {
            if (false === in_array($limit->getCardNumber(), $limitsCardNumbersOnModeration)) {
                $limitsCardNumbersOnModeration[] = $limit->getCardNumber();
            }
        }

        $this->targetRoute->save(['redirect' => $listQueryRequest->getData(), 'routeName' => $this->targetRoute->getRouteName()]);

        return new Response([
            'data' => $listQueryRequest->getData(),
            'result' => [
                'paginator' => $collection,
                'clients' => $clients,
                'limitsCardNumbersOnModeration' => $limitsCardNumbersOnModeration,
            ],
        ]);
    }
}
