<?php

namespace App\Clients\Action\Backend\Client\ProfileAction;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Infrastructure\ClientInfo\Criteria\ByClient;
use App\Users\Domain\User\User;
use CrudBundle\Action\Response;
use CrudBundle\Service\TargetRoute;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProfileAction
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
     * @var TargetRoute
     */
    private $targetRoute;

    public function __construct(
        Repository $clientRepository,
        Repository $userRepository,
        Repository $clientInfoRepository,
        Repository $refillBalanceRepository,
        TargetRoute $targetRoute
    ) {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->clientInfoRepository = $clientInfoRepository;
        $this->refillBalanceRepository = $refillBalanceRepository;
        $this->targetRoute = $targetRoute;
    }

    public function __invoke(string $id): Response
    {
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

        $company = $client->getCompany();
        $result = [
            'client' => $client,
            'company' => $company,
            'clintInfo' => $clientInfo,
            'manager' => $manager,
            'balanceUpdate' => $balanceUpdate,
            'availableBalance' => $availableBalance,
        ];

        $data = [];
        if ($company instanceof Company) {
            $data = [
                'accountingEmail' => $company->getAccounting()->getEmail(),
                'accountingPhone' => $company->getAccounting()->getPhone(),
                'email' => $company->getEmail(),
                'postalAddress' => $company->getPostalAddress(),
                'name' => $company->getName(),
            ];
        }

        $this->targetRoute->save(['id' => $client->getId()]);

        return new Response([
            'result' => $result,
            'data' => $data,
        ]);
    }
}
