<?php

namespace App\Api\Action\Api\V1\PartnersMe\ProfileAction;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Api\Resource\Model;
use App\Security\Partners\MyselfInterface;
use App\Users\Domain\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileAction
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
     * @var Model
     */
    private $profileResource;

    /**
     * @var JsonResponse
     */
    private $jsonResponse;

    public function __construct(
        MyselfInterface $myself,
        UserRepository $userRepository,
        Model $profileResource,
        JsonResponse $jsonResponse
    ) {
        $this->myself = $myself;
        $this->userRepository = $userRepository;
        $this->profileResource = $profileResource;
        $this->jsonResponse = $jsonResponse;
    }

    /**
     * @Route(
     *     "/api/v1/partners/me",
     *     name="api_v1_partners__me",
     *     methods={"GET"}
     * )
     */
    public function __invoke(): Response
    {
        $user = $this->myself->get();

        $partner = $user->getPartner();

        $manager = $this->userRepository->find([
            'manager1CId_equalTo' => $user->getPartner()->getManager1CId(),
        ]);

        $data = $this->profileResource->prepare([
            'user' => $user,
            'partner' => $partner,
            'manager' => $manager,
        ]);

        return $this->jsonResponse->createSuccessResponse($data);
    }
}
