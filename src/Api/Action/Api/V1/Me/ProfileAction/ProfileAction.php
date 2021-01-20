<?php

namespace App\Api\Action\Api\V1\Me\ProfileAction;

use App\Security\Cabinet\MyselfInterface;
use App\Users\Domain\User\Repository\UserRepository;
use App\Api\Resource\Model;
use App\Api\Crud\Interfaces\Response as JsonResponse;
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
     *     "/api/v1/me",
     *     name="api_v1_me",
     *     methods={"GET"}
     * )
     */
    public function __invoke(): Response
    {
        $user = $this->myself->get();

        $company = $this->myself->getCompany();
        $client = $this->myself->getClient();

        $manager = $this->userRepository->find([
            'manager1CId_equalTo' => $client->getManager1CId(),
        ]);

        $data = $this->profileResource->prepare([
            'user' => $user,
            'company' => $company,
            'manager' => $manager,
        ]);

        return $this->jsonResponse->createSuccessResponse($data);
    }
}
