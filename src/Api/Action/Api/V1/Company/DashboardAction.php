<?php

namespace App\Api\Action\Api\V1\Company;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Clients\Domain\Driver\ValueObject\Status as DriverStatus;
use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\ValueObject\Status as UserStatus;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardAction
{
    /**
     * @var MyselfInterface
     */
    private $myself;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Repository
     */
    private $driverRepository;
    /**
     * @var JsonResponse
     */
    private $jsonResponse;

    public function __construct(
        MyselfInterface $myself,
        UserRepository $userRepository,
        Repository $driverRepository,
        JsonResponse $jsonResponse
    ) {
        $this->myself = $myself;
        $this->userRepository = $userRepository;
        $this->driverRepository = $driverRepository;
        $this->jsonResponse = $jsonResponse;
    }

    /**
     * @Route(
     *     "/api/v1/company/dashboard",
     *     name="api_v1_company_dashboard",
     *     methods={"GET"}
     * )
     */
    public function __invoke(): Response
    {
        $myself = $this->myself->get();

        $company = $this->myself->getCompany();
        $activeUsersCount = $this->userRepository->count([
            'company_equalTo' => $company,
            'id_notEqualTo' => $myself->getId(),
            'status_equalTo' => UserStatus::active()->getValue(),
        ]);

        $client = $this->myself->getClient();
        $activeDriversCount = $this->driverRepository->count([
            'client1CId_equalTo' => $client->getClient1CId(),
            'status_equalTo' => DriverStatus::active()->getValue(),
        ]);

        $data = [
            'usersCount' => $activeUsersCount,
            'driversCount' => $activeDriversCount,
        ];

        return $this->jsonResponse->createSuccessResponse($data);
    }
}
