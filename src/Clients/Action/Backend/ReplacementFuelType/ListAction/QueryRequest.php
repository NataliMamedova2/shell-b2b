<?php

namespace App\Clients\Action\Backend\ReplacementFuelType\ListAction;

use App\Clients\Infrastructure\Client\Criteria\CardNumberCriteria;
use App\Clients\Infrastructure\Client\Criteria\ClientIdLike;
use App\Clients\Infrastructure\Client\Criteria\FullNameLike;
use App\Clients\Infrastructure\Client\Criteria\ManagerIdLike;
use App\Clients\Infrastructure\Client\Criteria\RegisterStatusCriteria;
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

        return $criteria;
    }

    public function getOrder(): array
    {
        return ['createdAt' => 'DESC'];
    }
}
