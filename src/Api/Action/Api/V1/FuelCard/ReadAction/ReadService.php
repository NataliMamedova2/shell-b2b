<?php

namespace App\Api\Action\Api\V1\FuelCard\ReadAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Api\DataTransformer\CardReadDataTransformer;
use App\Clients\Domain\Card\Card;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ReadService implements QueryHandler
{
    /**
     * @var Repository
     */
    private $fuelCardRepository;

    /**
     * @var MyselfInterface
     */
    private $myself;

    /**
     * @var CardReadDataTransformer
     */
    private $dataTransformer;

    public function __construct(
        Repository $fuelCardRepository,
        MyselfInterface $myself,
        CardReadDataTransformer $dataTransformer
    ) {
        $this->fuelCardRepository = $fuelCardRepository;
        $this->myself = $myself;
        $this->dataTransformer = $dataTransformer;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $params = $queryRequest->getQueryParams();
        $id = $params['id'] ?? null;

        $client = $this->myself->getClient();
        /** @var Card $card */
        $card = $this->fuelCardRepository->find([
            'id_equalTo' => $id,
            'client1CId_equalTo' => $client->getClient1CId(),
        ]);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException('Card not found');
        }

        if (true === $card->isBlocked() || true === $card->cardInStopList()) {
            throw new NotFoundHttpException('Card is blocked');
        }

        return $this->dataTransformer->transform($card);
    }
}
