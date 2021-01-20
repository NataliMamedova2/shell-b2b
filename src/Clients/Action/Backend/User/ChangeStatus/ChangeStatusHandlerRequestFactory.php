<?php

namespace App\Clients\Action\Backend\User\ChangeStatus;

use App\Clients\Domain\User\UseCase\ChangeStatus\HandlerRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ChangeStatusHandlerRequestFactory
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    public function __construct(
        RequestStack $requestStack,
        AuthorizationCheckerInterface $authorizationChecker,
        DenormalizerInterface $denormalizer
    ) {
        $request = $requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->authorizationChecker = $authorizationChecker;
        $this->denormalizer = $denormalizer;
    }

    /**
     * @IsGranted({"ROLE_ADMIN", "ROLE_SUPER_ADMIN", "ROLE_MANAGER", "ROLE_MANAGER_CALL_CENTER" })
     */
    public function __invoke(): HandlerRequest
    {
        $data = $this->request->request->all();

        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->denormalizer->denormalize($data, HandlerRequest::class);

        $userId = $this->request->get('id');
        $handlerRequest->setId($userId);

        return $handlerRequest;
    }
}
