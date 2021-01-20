<?php

namespace App\Clients\Action\Backend\Transaction\ListAction;

use App\Clients\Domain\Transaction\Card\Transaction;
use App\Clients\Infrastructure\Client\Criteria\IndexByClient1CId;
use App\Clients\Infrastructure\Fuel\Criteria\IndexByFuelCode;
use App\Clients\Infrastructure\FuelCard\Criteria\IndexByCardNumber;
use App\Clients\Infrastructure\Transaction\Repository\TransactionRepository;
use App\Users\Domain\User\User;
use App\Users\Domain\User\ValueObject\Role as UserRoleValueObj;
use App\Users\Infrastructure\Criteria\Role;
use CrudBundle\Action\Response;
use CrudBundle\Interfaces\TargetRoute;
use Infrastructure\Interfaces\Paginator\Paginator;
use Infrastructure\Interfaces\Repository\Repository;
use Pagerfanta\Pagerfanta;

final class ListAction
{
    /** @var int */
    private const LIMIT = 1250;

    /**
     * @var Paginator
     */
    private $transactionPaginator;

    /**
     * @var Repository
     */
    private $clientRepository;

    /**
     * @var Repository
     */
    private $cardRepository;

    /**
     * @var Repository
     */
    private $fuelTypeRepository;

    /**
     * @var Repository
     */
    private $userRepository;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var TargetRoute
     */
    private $targetRoute;

    /**
     * @var QueryRequest
     */
    private $request;

    public function __construct(
        Paginator $transactionPaginator,
        Repository $clientRepository,
        Repository $cardRepository,
        Repository $fuelTypeRepository,
        Repository $userRepository,
        TransactionRepository $transactionRepository,
        TargetRoute $targetRoute
    ) {
        $this->transactionPaginator = $transactionPaginator;
        $this->clientRepository = $clientRepository;
        $this->cardRepository = $cardRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
        $this->targetRoute = $targetRoute;
    }

    public function __invoke(QueryRequest $listQueryRequest): Response
    {
        /* @var QueryRequest request */
        $this->request = $listQueryRequest;

        /** @var Pagerfanta $collection */
        $collection = $this->transactionPaginator->paginate(
            $this->request->getCriteria(),
            $this->request->getOrder(),
            $this->request->getPage(),
            $this->request->getLimit()
        );

        $client1CId = [];
        $fuelCodes = [];
        $cardsNumbers = [];
        $volumeSum = 0;
        $debitSum = 0;

        foreach ($collection as $entity) {
            $client1CId[$entity->getClient1CId()] = $entity->getClient1CId();
            $fuelCodes[$entity->getFuelCode()] = $entity->getFuelCode();
            $cardsNumbers[$entity->getCardNumber()] = $entity->getCardNumber();
        }

        if ($this->isShowAddInfo()) {
            $volumeSum = $this->transactionRepository->calculateSumReportOnField($this->request->getCriteria(), 'fuelQuantity');
            $debitSum = $this->transactionRepository->calculateSumReportOnField($this->request->getCriteria(), 'debit');
        }

        $clients = $this->clientRepository->findMany([
            'client1CId_in' => array_values($client1CId),
            IndexByClient1CId::class => true,
        ]);

        $cards = $this->cardRepository->findMany([
            'cardNumber_in' => array_values($cardsNumbers),
            IndexByCardNumber::class => true,
        ]);

        $supplies = $this->fuelTypeRepository->findMany([
            'fuelCode_in' => array_values($fuelCodes),
            IndexByFuelCode::class => true,
        ]);

        $this->targetRoute->save();

        $allowExport = false;

        if (count($clients) >= 1 && isset($listQueryRequest->getData()['dateFrom']) && !empty($listQueryRequest->getData()['dateFrom'])) {
            $allowExport = true;
        }

        if ($collection->getNbResults() > self::LIMIT) {
            $allowExport = false;
        }

        /** @var User[] $managersCollection */
        $managersCollection = $this->userRepository->findMany([
            Role::class => UserRoleValueObj::getManagerName(),
            'manager1CId_isNotNull' => true,
        ]);
        $managersArr = [];

        foreach ($managersCollection as $manager) {
            $managersArr[$manager->getManager1CId()] = $manager->getName();
        }

        $filterValueForManager = ($this->isUserManager()) ? $this->getUserManagerId() : '';

        return new Response([
            'data' => $this->request->getData(),
            'result' => [
                'managers' => $managersArr,
                'isShowManagerFilter' => $this->isUserManagerOrAdmin(),
                'isShowAddInfo' => $this->isShowAddInfo(),
                'filterValueForManager' => $filterValueForManager,
                'paginator' => $collection,
                'clients' => $clients,
                'cards' => $cards,
                'supplies' => $supplies,
                'allowExport' => $allowExport,
                'volumeSum' => $volumeSum,
                'debitSum' => $debitSum,
            ],
        ]);
    }

    public function isUserManagerOrAdmin(): bool
    {
        return $this->request->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')
            || $this->request->authorizationChecker->isGranted('ROLE_ADMIN')
            || $this->request->authorizationChecker->isGranted('ROLE_MANAGER')
            || $this->request->authorizationChecker->isGranted('ROLE_MANAGER_CALL_CENTER');
    }

    public function isUserManager(): bool
    {
        return $this->request->authorizationChecker->isGranted('ROLE_MANAGER');
    }

    public function isShowAddInfo(): bool
    {
        return $this->isUserManagerOrAdmin() && isset($this->request->getData()['dateFrom']) && null != $this->request->getData()['dateFrom'];
    }

    public function getUserManagerId(): string
    {
        return $this->request->user->getManager1CId();
    }
}
