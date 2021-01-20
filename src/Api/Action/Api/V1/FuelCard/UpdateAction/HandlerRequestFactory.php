<?php

namespace App\Api\Action\Api\V1\FuelCard\UpdateAction;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\UseCase\Update\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

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
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    public function __construct(
        RequestStack $requestStack,
        Repository $cardRepository,
        MyselfInterface $myself,
        DenormalizerInterface $denormalizer
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->cardRepository = $cardRepository;
        $this->myself = $myself;
        $this->denormalizer = $denormalizer;
    }

    public function __invoke()
    {
        $id = $this->request->attributes->get('id');
        $client = $this->myself->getClient();
        $card = $this->cardRepository->find([
            'id_equalTo' => $id,
            'client1CId_equalTo' => $client->getClient1CId(),
        ]);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException('Card not found');
        }

        if (true === $card->isBlocked() || true === $card->cardInStopList()) {
            throw new HttpException(404, 'Card is blocked');
        }

        if (true === $card->getExportStatus()->onModeration()) {
            throw new HttpException(404, 'Card on moderation');
        }

        $data = $this->request->request->all();

        $handlerRequest = new HandlerRequest($id);

        $context = [
            AbstractNormalizer::OBJECT_TO_POPULATE => $handlerRequest,
        ];
        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->denormalizer->denormalize($data, HandlerRequest::class, null, $context);

        $handlerRequest->startUseTime = (new \DateTimeImmutable($this->request->get('startUseTime')))->format('H:i:s');
        $handlerRequest->endUseTime = (new \DateTimeImmutable($this->request->get('endUseTime')))->format('H:i:s');

        return $handlerRequest;
    }
}
