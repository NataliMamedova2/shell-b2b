<?php

namespace App\Api\Action\Api\V1\FuelCard\ChangeDriverAction;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\UseCase\ChangeDriver\HandlerRequest;
use App\Clients\Domain\Card\ValueObject\CardId;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use App\Clients\Domain\Driver\ValueObject\Status;
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
     * @var MyselfInterface
     */
    private $myself;

    /**
     * @var Repository
     */
    private $cardRepository;

    /**
     * @var Repository
     */
    private $driverRepository;

    public function __construct(
        RequestStack $requestStack,
        MyselfInterface $myself,
        Repository $cardRepository,
        Repository $driverRepository
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->myself = $myself;
        $this->cardRepository = $cardRepository;
        $this->driverRepository = $driverRepository;
    }

    public function __invoke(): HandlerRequest
    {
        $cardId = $this->request->get('id');

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

        $driverId = DriverId::fromString((string) $this->request->get('driverId'));
        $driver = $this->driverRepository->find([
            'id_equalTo' => $driverId->getId(),
            'client1CId_equalTo' => $client->getClient1CId(),
            'status_equalTo' => Status::active()->getValue(),
        ]);

        if (!$driver instanceof Driver) {
            throw new NotFoundHttpException('Driver not found');
        }

        return new HandlerRequest(CardId::fromString($card->getId()), $driver);
    }
}
