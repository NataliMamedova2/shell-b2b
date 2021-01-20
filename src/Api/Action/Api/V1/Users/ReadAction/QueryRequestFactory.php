<?php

namespace App\Api\Action\Api\V1\Users\ReadAction;

use App\Security\Cabinet\Myself;
use Symfony\Component\HttpFoundation\RequestStack;

final class QueryRequestFactory
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Myself
     */
    private $myself;

    public function __construct(RequestStack $requestStack, Myself $myself)
    {
        $this->requestStack = $requestStack;
        $this->myself = $myself;
    }

    public function __invoke()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request no found');
        }

        return new QueryRequest($request, $this->myself);
    }
}
