<?php

namespace App\Feedback\Action\Backend\ListAction;

use App\Feedback\Infrastructure\Criteria\ManagerCriteria;
use App\Users\Domain\User\User;
use CrudBundle\Action\ListAction\QueryRequest as BaseQueryRequest;
use CrudBundle\Interfaces\ListQueryRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class QueryRequest extends BaseQueryRequest implements ListQueryRequest
{
    /**
     * @var UserInterface|User
     */
    private $user;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $token = $tokenStorage->getToken();
        if (null === $token) {
            throw new \InvalidArgumentException('Token not found');
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            throw new \InvalidArgumentException('User not found');
        }

        $this->user = $user;
        $this->authorizationChecker = $authorizationChecker;

        parent::__construct($request);
    }

    public function getCriteria(): array
    {
        $criteria = [];

        if ($this->user instanceof User && true === $this->authorizationChecker->isGranted('ROLE_MANAGER')) {
            $criteria[ManagerCriteria::class] = $this->user->getManager1CId();
        }

        return $criteria;
    }

    public function getOrder(): array
    {
        return ['createdAt' => 'DESC'];
    }
}
