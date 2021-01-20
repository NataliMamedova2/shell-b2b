<?php

declare(strict_types=1);

namespace App\Api\Action\Backend\LogListAction;

use App\Api\Infractructure\Criteria\CodeCriteria;
use App\Api\Infractructure\Criteria\MethodCriteria;
use CrudBundle\Action\ListAction\QueryRequest as BaseQueryRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class QueryRequest extends BaseQueryRequest
{
    public function __construct(RequestStack $requestStack, AuthorizationCheckerInterface $authorizationChecker)
    {
        if (false === $authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied');
        }

        parent::__construct($requestStack->getCurrentRequest());
    }

    public function getCriteria(): array
    {
        $request = $this->getRequest();

        $criteria = [];

        if ($resource = $request->get('resource')) {
            $criteria['resource_like'] = "%$resource%";
        }
        if (null != (string) $request->get('code')) {
            $criteria[CodeCriteria::class] = (int) $request->get('code');
        }
        if (null != (string) $request->get('method')) {
            $criteria[MethodCriteria::class] = $request->get('method');
        }

        return $criteria;
    }

    public function getOrder(): array
    {
        return ['createdAt' => 'DESC'];
    }
}
