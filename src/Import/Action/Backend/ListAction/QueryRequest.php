<?php

namespace App\Import\Action\Backend\ListAction;

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

        if ($filename = $request->get('filename')) {
            $criteria['fileName_like'] = "%$filename%";
        }
        if (null != (string) $request->get('extension')) {
            $criteria['extension_equalTo'] = $request->get('extension');
        }
        if (null != (string) $request->get('status')) {
            $criteria['status_equalTo'] = $request->get('status');
        }

        return $criteria;
    }

    public function getOrder(): array
    {
        return ['createdAt' => 'DESC', 'fileName' => 'DESC'];
    }
}
