<?php

namespace App\Api\Action\Api\V1\Users\Documents;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Users\Domain\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class QueryRequest
{
    /** @var string */
    private const COMPANY_NAME = 'ТОВАРИСТВО З ОБМЕЖЕНОЮ ВИДПОВИДАЛЬНИСТЮ АЛЬЯНС ХОЛДИНГ';

    /** @var string */
    private const EDRPOU = '44444448';

    /** @var RequestStack */
    private $request;

    /** @var JsonResponse */
    private $jsonResponse;

    /** @var UserRepository */
    private $userRepository;

    /** @var RouterInterface */
    private $router;

    public function __construct(RequestStack $request, JsonResponse $jsonResponse, UserRepository $userRepository, RouterInterface $router)
    {
        $this->request = $request;
        $this->jsonResponse = $jsonResponse;
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    /**
     * @Route(
     *     "/api/v1/sota-clients/admins/client",
     *     name="api_v1_sota_clients_admins_client",
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

            $user = $this->userRepository->find(['sotaToken_equalTo' => $token]);
            if (null === $user) {
                return new RedirectResponse($this->router->generate('api_v1_sota_partners_user', ['token' => $token]));
            }

            $name = \iconv(\mb_detect_encoding(self::COMPANY_NAME), 'UTF-8', self::COMPANY_NAME);

            $requestedClientInfo = [
                'Id' => $user->getId(),
                'Email' => $user->getEmail() ?? '',
                'Phone' => $user->getPhone() ?? '',
                'Name' => $name ?? '',
                'Orgs' => [[
                    'Edrpou' => self::EDRPOU,
                    'Name' => \iconv(\mb_detect_encoding($user->getName()), 'UTF-8', $user->getName()),
                ]],
            ];
        } catch (\Throwable $exception) {
            return $this->jsonResponse->createErrorResponse($exception->getMessage());
        }

        return $this->jsonResponse->createSuccessResponse($requestedClientInfo);
    }
}
