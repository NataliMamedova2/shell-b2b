<?php

namespace App\Users\Action\Backend\ListAction;

use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequestFactory
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request no found');
        }

        return new QueryRequest($request);
    }
}
