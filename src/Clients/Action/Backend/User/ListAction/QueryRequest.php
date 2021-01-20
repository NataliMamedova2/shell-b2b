<?php

namespace App\Clients\Action\Backend\User\ListAction;

use App\Clients\Infrastructure\User\Criteria\ClientIdLike;
use App\Clients\Infrastructure\User\Criteria\CompanyLike;
use App\Clients\Infrastructure\User\Criteria\EmailLike;
use App\Clients\Infrastructure\User\Criteria\FullNameLike;
use App\Clients\Infrastructure\User\Criteria\ManagerId;
use CrudBundle\Action\ListAction\QueryRequest as BaseQueryRequest;
use CrudBundle\Interfaces\ListQueryRequest;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Status;
use App\Clients\Infrastructure\User\Criteria\Role as RoleCriteria;
use App\Users\Domain\User\User;
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
        $request = $this->getRequest();
        $criteria = [];

        if (null != (string) $request->get('status')) {
            $criteria['status_equalTo'] = Status::fromName($request->get('status'));
        }

        if (null != (string) $request->get('role')) {
            $criteria[RoleCriteria::class] = (string) Role::fromName($request->get('role'));
        }

        if (null != (string) $request->get('company')) {
            $criteria[CompanyLike::class] = $request->get('company');
        }

        if (null != (string) $request->get('client1cId')) {
            $criteria[ClientIdLike::class] = $request->get('client1cId');
        }

        if (null != (string) $request->get('email')) {
            $criteria[EmailLike::class] = $request->get('email');
        }

        if (null != (string) $request->get('fullName')) {
            $criteria[FullNameLike::class] = $request->get('fullName');
        }

        if ($this->user instanceof User && true === $this->authorizationChecker->isGranted('ROLE_MANAGER')) {
            $criteria[ManagerId::class] = $this->user->getManager1CId();
        }

        return $criteria;
    }

    public function getOrder(): array
    {
        return ['createdAt' => 'DESC'];
    }
}
