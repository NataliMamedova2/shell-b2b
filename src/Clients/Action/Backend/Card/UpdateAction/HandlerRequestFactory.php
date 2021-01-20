<?php

namespace App\Clients\Action\Backend\Card\UpdateAction;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\UseCase\Update\HandlerRequest;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    public function __construct(
        RequestStack $requestStack,
        Repository $cardRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        DenormalizerInterface $denormalizer
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->cardRepository = $cardRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->denormalizer = $denormalizer;
    }

    public function __invoke()
    {
        $id = $this->request->attributes->get('id');
        $card = $this->cardRepository->find([
            'id_equalTo' => $id,
            'status_equalTo' => CardStatus::active()->getValue(),
            ExportStatusCriteria::class => ExportStatus::canBeEditedStatuses(),
        ]);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException('Card not found');
        }

        if (false === $this->authorizationChecker->isGranted('view', $card)) {
            throw new AccessDeniedException('Access Denied');
        }

        $data = $this->request->request->all();

        $handlerRequest = new HandlerRequest($id);
        $context = [
            AbstractNormalizer::OBJECT_TO_POPULATE => $handlerRequest,
        ];
        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->denormalizer->denormalize($data, HandlerRequest::class, null, $context);

        list('hour' => $hour, 'minute' => $minute) = $this->request->get('startUseTime');
        $handlerRequest->startUseTime = (new \DateTimeImmutable("$hour:$minute"))->format('H:i:s');

        list('hour' => $hour, 'minute' => $minute) = $this->request->get('endUseTime');
        $handlerRequest->endUseTime = (new \DateTimeImmutable("$hour:$minute"))->format('H:i:s');

        return $handlerRequest;
    }
}
