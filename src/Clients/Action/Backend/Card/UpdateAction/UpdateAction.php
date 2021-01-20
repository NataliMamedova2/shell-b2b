<?php

namespace App\Clients\Action\Backend\Card\UpdateAction;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\UseCase\Update\Handler;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use CrudBundle\Action\Response;
use CrudBundle\Interfaces\RedirectResponse;
use Domain\Exception\DomainException;
use Domain\Interfaces\HandlerRequest;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UpdateAction
{
    /**
     * @var Repository
     */
    private $cardRepository;
    /**
     * @var Repository
     */
    private $cardLimitsRepository;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var Handler
     */
    private $handler;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        Repository $cardRepository,
        Repository $cardLimitsRepository,
        ValidatorInterface $validator,
        Handler $handler,
        FlashBagInterface $flashBag
    ) {
        $this->cardRepository = $cardRepository;
        $this->cardLimitsRepository = $cardLimitsRepository;
        $this->validator = $validator;
        $this->handler = $handler;
        $this->flashBag = $flashBag;
    }

    public function __invoke(
        string $id,
        HandlerRequest $handlerRequest,
        RedirectResponse $redirectResponse
    ) {
        $card = $this->cardRepository->find([
            'id_equalTo' => $id,
            'status_equalTo' => CardStatus::active()->getValue(),
        ]);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException();
        }
        $countLimitsOnModeration = $this->cardLimitsRepository->count([
            'cardNumber_equalTo' => $card->getCardNumber(),
            ExportStatusCriteria::class => ExportStatus::cantBeEditedStatuses(),
        ]);

        $errors = $this->validator->validate($handlerRequest);
        if (count($errors) > 0) {
            $this->flashBag->set('error', 'save_data.error');

            $handlerRequest = $this->castHandlerRequest($handlerRequest);

            return new Response([
                'data' => $handlerRequest,
                'result' => [
                    'card' => $card,
                    'haveLimitsOnModeration' => $countLimitsOnModeration > 0 ? true : false,
                ],
                'errors' => $errors,
            ]);
        }

        try {
            $result = $this->handler->handle($handlerRequest);

            $this->flashBag->set('success', 'save_data.success');

            $response = new Response([
                'data' => $handlerRequest,
                'result' => $result,
                'errors' => [],
            ]);

            return $redirectResponse->redirect($response);
        } catch (DomainException $e) {
            $this->flashBag->set('error', $e->getMessage());

            $handlerRequest = $this->castHandlerRequest($handlerRequest);

            return new Response([
                'data' => $handlerRequest,
                'errors' => [
                    'exception' => $e->getMessage(),
                ],
            ]);
        }
    }

    private function castHandlerRequest(HandlerRequest $handlerRequest): HandlerRequest
    {
        if ($handlerRequest instanceof \App\Clients\Domain\Card\UseCase\Update\HandlerRequest) {
            if (!$handlerRequest->startUseTime instanceof \DateTimeInterface) {
                $handlerRequest->startUseTime = new \DateTimeImmutable($handlerRequest->startUseTime);
            }
            if (!$handlerRequest->endUseTime instanceof \DateTimeInterface) {
                $handlerRequest->endUseTime = new \DateTimeImmutable($handlerRequest->endUseTime);
            }
        }

        return $handlerRequest;
    }
}
