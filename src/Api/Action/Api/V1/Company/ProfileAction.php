<?php

namespace App\Api\Action\Api\V1\Company;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Api\Resource\Company;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileAction
{
    /**
     * @var MyselfInterface
     */
    private $myself;
    /**
     * @var JsonResponse
     */
    private $jsonResponse;

    public function __construct(
        MyselfInterface $myself,
        JsonResponse $jsonResponse
    ) {
        $this->myself = $myself;
        $this->jsonResponse = $jsonResponse;
    }

    /**
     * @Route(
     *     "/api/v1/company/profile",
     *     name="api_v1_company_profile",
     *     methods={"GET"}
     * )
     *
     * @throws \Exception
     */
    public function __invoke(): Response
    {
        $user = $this->myself->get();
        $company = $user->getCompany();

        $companyResource = new Company();
        $data = $companyResource->prepare($company);

        return $this->jsonResponse->createSuccessResponse($data);
    }
}
