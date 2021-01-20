<?php

namespace App\Api\Action\Api\V1\Documents\SotaIntegration\Api;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Clients\Infrastructure\User\Repository\UserRepository as CompanyUserRepository;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class QueryRequest
{
    /** @var RequestStack */
    private $request;

    /** @var JsonResponse */
    private $jsonResponse;

    /** @var Repository */
    private $clientRepository;

    /** @var CompanyUserRepository */
    private $userRepository;

    /** @var RouterInterface */
    private $router;

    /**
     * SotaIntegrationAction constructor.
     * @param RequestStack $requestStack
     * @param JsonResponse $jsonResponse
     * @param Repository $clientRepository
     * @param CompanyUserRepository $userRepository
     * @param RouterInterface $router
     */
    public function __construct(
        RequestStack $requestStack,
        JsonResponse $jsonResponse,
        Repository $clientRepository,
        CompanyUserRepository $userRepository,
        RouterInterface $router
    ) {
        $this->request = $requestStack;
        $this->jsonResponse = $jsonResponse;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    /**
     * @Route(
     *     "/api/v1/sota-clients/client",
     *     name="api_v1_sota_clients_client",
     *     methods={"GET"}
     * )
     */
    public function __invoke(): Response
    {
        try {
            $token = null;

            if (null !== $this->request->getCurrentRequest()) {
                $token = $this->request->getCurrentRequest()->query->get('token');
            }

            if (null === $token) {
                throw new \Exception('token not be null');
            }

            $client = $this->clientRepository->find(['sotaToken_equalTo' => $token]);
            if (null === $client) {
                return new RedirectResponse($this->router->generate('api_v1_sota_clients_admins_client', ['token' => $token]));
            }

            $company = $client->getCompany();
            if (null === $company) {
                throw new \Exception('client not found');
            }

            $name = \iconv(\mb_detect_encoding($company->getName()), 'UTF-8', $company->getName());

            $companyUser = $this->userRepository->find(['company_equalTo' => $company->getId()]);
            if (null === $companyUser) {
                throw new \Exception('client not found');
            }
            $email = $companyUser->getEmail();

            $requestedClientInfo = [
                'Id' => $client->getId(),
                'Email' => $email ?? '',
                'Phone' => $companyUser->getPhone() ?? '',
                'Name' => $name ?? '',
                'Orgs' => [[
                    'Edrpou' => $client->getEdrpouInn(),
                    'Name' => \iconv(\mb_detect_encoding($client->getFullName()), 'UTF-8', $client->getFullName()),
                ]],
            ];
        } catch (\Throwable $exception) {
            return $this->jsonResponse->createErrorResponse($exception->getMessage());
        }

        return $this->jsonResponse->createSuccessResponse($requestedClientInfo);
    }
}
