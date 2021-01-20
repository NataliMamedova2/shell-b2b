<?php

namespace App\Clients\Action\Backend\Client\ListAction;

use App\Clients\Domain\Client\Client;
use App\Clients\Infrastructure\ClientInfo\Criteria\IndexByClientPсId;
use App\Clients\Infrastructure\User\Repository\UserRepository as CompanyUserRepository;
use App\Users\Infrastructure\Criteria\IndexByManagerId;
use App\Users\Infrastructure\Repository\UserRepository;
use CrudBundle\Action\Response;
use CrudBundle\Interfaces\ListQueryRequest;
use CrudBundle\Service\TargetRoute;
use Infrastructure\Interfaces\Paginator\Paginator;
use Infrastructure\Interfaces\Repository\Repository;

final class ListAction
{
    /**
     * @var Repository
     */
    private $clientInfoRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CompanyUserRepository
     */
    private $companyUserRepository;
    /**
     * @var TargetRoute
     */
    private $targetRoute;

    public function __construct(
        Repository $clientInfoRepository,
        UserRepository $userRepository,
        CompanyUserRepository $companyUserRepository,
        TargetRoute $targetRoute
    ) {
        $this->clientInfoRepository = $clientInfoRepository;
        $this->userRepository = $userRepository;
        $this->companyUserRepository = $companyUserRepository;
        $this->targetRoute = $targetRoute;
    }

    public function __invoke(ListQueryRequest $listQueryRequest, Paginator $paginator): Response
    {
        /** @var Client[] $clients */
        $clients = $paginator->paginate(
            $listQueryRequest->getCriteria(),
            $listQueryRequest->getOrder(),
            $listQueryRequest->getPage(),
            $listQueryRequest->getLimit()
        );

        $clientPcIds = [];
        $managersIds = [];
        foreach ($clients as $client) {
            $clientPcIds[] = $client->getClientPcId();
            $managersIds[] = $client->getManager1CId();
        }

        $clientInfoList = $this->clientInfoRepository->findMany([
            'clientPcId_in' => $clientPcIds,
            IndexByClientPсId::class => true,
        ]);

        $managers = $this->userRepository->findMany([
            'manager1CId_in' => $managersIds,
            IndexByManagerId::class => true,
        ]);

        $adminsInCompanyCount = [];

        foreach ($this->companyUserRepository->getAdminInCompanyCount() as $elem) {
            $adminsInCompanyCount[(string) $elem['id']] = $elem['count'];
        }

        $this->targetRoute->save(['redirect' => $listQueryRequest->getData(), 'routeName' => $this->targetRoute->getRouteName()]);

        return new Response([
            'data' => $listQueryRequest->getData(),
            'result' => [
                'paginator' => $clients,
                'clientInfo' => $clientInfoList,
                'managers' => $managers,
                'adminsInCompanyCount' => $adminsInCompanyCount,
            ],
        ]);
    }
}
