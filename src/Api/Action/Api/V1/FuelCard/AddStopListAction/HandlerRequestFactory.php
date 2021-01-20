<?php

namespace App\Api\Action\Api\V1\FuelCard\AddStopListAction;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\UseCase\AddStopList\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class HandlerRequestFactory
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Repository
     */
    private $cardRepository;

    /**
     * @var MyselfInterface
     */
    private $myself;

    public function __construct(
        RequestStack $requestStack,
        Repository $cardRepository,
        MyselfInterface $myself
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->cardRepository = $cardRepository;
        $this->myself = $myself;
    }

    public function __invoke()
    {
        $cardId = $this->request->attributes->get('id');

        $client = $this->myself->getClient();
        $card = $this->cardRepository->find([
            'id_equalTo' => $cardId,
            'client1CId_equalTo' => $client->getClient1CId(),
        ]);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException('Card not found');
        }

        if (true === $card->isBlocked()) {
            throw new HttpException(404, 'Card is blocked');
        }

        if (true === $card->getExportStatus()->onModeration()) {
            throw new HttpException(404, 'Card on moderation');
        }

        $handlerRequest = new HandlerRequest();
        $handlerRequest->card = $card;

        return $handlerRequest;
    }
}
