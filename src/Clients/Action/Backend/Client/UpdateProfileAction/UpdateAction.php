<?php

namespace App\Clients\Action\Backend\Client\UpdateProfileAction;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\UseCase\UpdateProfile\Handler;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Infrastructure\ClientInfo\Criteria\ByClient;
use App\Users\Domain\User\User;
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
    private $clientRepository;

    /**
     * @var Repository
     */
    private $userRepository;

    /**
     * @var Repository
     */
    private $refillBalanceRepository;

    /**
     * @var Repository
     */
    private $clientInfoRepository;
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
        Repository $clientRepository,
        Repository $userRepository,
        Repository $clientInfoRepository,
        Repository $refillBalanceRepository,
        ValidatorInterface $validator,
        Handler $handler,
        FlashBagInterface $flashBag
    ) {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->clientInfoRepository = $clientInfoRepository;
        $this->refillBalanceRepository = $refillBalanceRepository;
        $this->validator = $validator;
        $this->handler = $handler;
        $this->flashBag = $flashBag;
    }

    public function __invoke(
        string $id,
        HandlerRequest $handlerRequest,
        RedirectResponse $redirectResponse
    ) {

        $client = $this->clientRepository->findById($id);

        if (!$client instanceof Client) {
            throw new NotFoundHttpException();
        }

        /** @var User $manager */
        $manager = $this->userRepository->find([
            'manager1CId_equalTo' => $client->getManager1CId(),
        ]);

        /** @var ClientInfo $clientInfo */
        $clientInfo = $this->clientInfoRepository->find([
            ByClient::class => $client,
        ]);

        /** @var RefillBalance|null $lastBalanceUpdate */
        $lastBalanceUpdate = $this->refillBalanceRepository->find([
            'fcCbrId_equalTo' => $client->getClientPcId(),
        ], ['operationDate' => 'DESC']);

        $balanceUpdate = [];
        if ($lastBalanceUpdate instanceof RefillBalance && !empty($lastBalanceUpdate->getAmount())) {
            $balanceUpdate = [
                'balance' => [
                    'value' => abs($lastBalanceUpdate->getAmount()),
                    'sign' => $lastBalanceUpdate->getOperationSign(),
                ],
                'dateTime' => $lastBalanceUpdate->getOperationDateTime(),
            ];
        }

        if ($clientInfo->getBalance() > 0) {
            $availableBalance = $clientInfo->getCreditLimit() + abs($clientInfo->getBalance());
        } else {
            $availableBalance = $clientInfo->getCreditLimit() - abs($clientInfo->getBalance());
        }

        /** @var Company $company */
        $company = $client->getCompany();

        $result = [
            'client' => $client,
            'company' => $company,
            'clintInfo' => $clientInfo,
            'manager' => $manager,
            'balanceUpdate' => $balanceUpdate,
            'availableBalance' => $availableBalance,
        ];

        $errors = $this->validator->validate($handlerRequest);
        if (count($errors) > 0) {
            $this->flashBag->set('error', 'save_data.error');

            return new Response([
                'data' => $handlerRequest,
                'result' => $result,
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

            return new Response([
                'data' => $handlerRequest,
                'errors' => [
                    'exception' => $e->getMessage(),
                ],
            ]);
        }
    }
}
