<?php

namespace App\Api\Action\Api\V1\Partners\Documents\SotaIntegration;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Partners\Domain\Partner\Partner;
use App\Partners\Domain\User\User;
use App\Partners\Infrastructure\Partner\Repository\PartnerRepository;
use App\Partners\Infrastructure\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class QueryRequest
{
    /** @var RequestStack */
    private $request;

    /** @var JsonResponse */
    private $jsonResponse;

    /** @var PartnerRepository */
    private $partnerRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        RequestStack $request,
        JsonResponse $jsonResponse,
        PartnerRepository $partnerRepository,
        UserRepository $userRepository
    ) {
        $this->request = $request;
        $this->jsonResponse = $jsonResponse;
        $this->partnerRepository = $partnerRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(
     *     "/api/v1/sota-clients/partners/user",
     *     name="api_v1_sota_partners_user",
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

            /** @var Partner $partner */
            $partner = $this->partnerRepository->find(['sotaToken_equalTo' => $token]);
            if (null == $partner) {
                throw new \Exception('user not found');
            }

            $name = \iconv(\mb_detect_encoding($partner->getTitle()), 'UTF-8', $partner->getTitle());
            /** @var User $user */
            $user = $this->userRepository->find(['partner_equalTo' => $partner->getId()]);

            if (null == $user) {
                throw new \Exception('user not found');
            }

            $requestedClientInfo = [
                'Id' => $partner->getId(),
                'Email' => $user->getEmail() ?? '',
                'Phone' => $user->getPhone() ?? '',
                'Name' => $name ?? '',
                'Orgs' => [[
                    'Edrpou' => $partner->getEdrpou(),
                    'Name' => \iconv(\mb_detect_encoding($user->getName()), 'UTF-8', $user->getName()),
                ]],
            ];
        } catch (\Throwable $exception) {
            return $this->jsonResponse->createErrorResponse($exception->getMessage());
        }

        return $this->jsonResponse->createSuccessResponse($requestedClientInfo);
    }
}
