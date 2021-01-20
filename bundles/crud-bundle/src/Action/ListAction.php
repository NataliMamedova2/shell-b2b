<?php

namespace CrudBundle\Action;

use CrudBundle\Interfaces\ListQueryRequest;
use CrudBundle\Service\TargetRoute;
use Infrastructure\Interfaces\Paginator\Paginator;

final class ListAction
{
    /**
     * @var TargetRoute
     */
    private $targetRoute;

    public function __construct(TargetRoute $targetRoute)
    {
        $this->targetRoute = $targetRoute;
    }

    public function __invoke(ListQueryRequest $listQueryRequest, Paginator $paginator): Response
    {
        $result = $paginator->paginate(
            $listQueryRequest->getCriteria(),
            $listQueryRequest->getOrder(),
            $listQueryRequest->getPage(),
            $listQueryRequest->getLimit()
        );

        $this->targetRoute->save(['redirect' => $listQueryRequest->getData(), 'routeName' => $this->targetRoute->getRouteName()]);

        return new Response([
            'data' => $listQueryRequest->getData(),
            'result' => $result,
        ]);
    }
}
