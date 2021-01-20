<?php

namespace App\Clients\Action\Backend\Card\BlockAction;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\UseCase\AddStopList\HandlerRequest;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    public function __construct(
        RequestStack $requestStack,
        Repository $cardRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->cardRepository = $cardRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function __invoke()
    {
        $id = $this->request->attributes->get('id');
        $card = $this->cardRepository->findById($id);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException('Card not found');
        }

        if (false === $this->authorizationChecker->isGranted('view', $card)) {
            throw new AccessDeniedException('Access Denied');
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
