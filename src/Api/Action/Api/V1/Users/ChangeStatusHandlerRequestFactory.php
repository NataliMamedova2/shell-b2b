<?php

namespace App\Api\Action\Api\V1\Users;

use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\UseCase\ChangeStatus\HandlerRequest;
use App\Clients\Domain\User\User;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class ChangeStatusHandlerRequestFactory
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var MyselfInterface
     */
    private $myself;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    public function __construct(
        RequestStack $requestStack,
        MyselfInterface $myself,
        UserRepository $userRepository
    ) {
        $this->requestStack = $requestStack;
        $this->myself = $myself;
        $this->userRepository = $userRepository;
        $this->normalizer = new ObjectNormalizer();
    }

    public function __invoke(): HandlerRequest
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $myself = $this->myself->get();
        $userId = $request->get('id');

        $company = $this->myself->get()->getCompany();
        $user = $this->userRepository->find([
            'company_equalTo' => $company,
            'id_equalTo' => $userId,
            'id_notEqualTo' => $myself->getId(),
        ]);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        $data = $request->request->all();

        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->normalizer->denormalize($data, HandlerRequest::class);
        $handlerRequest->setId($userId);

        return $handlerRequest;
    }
}
