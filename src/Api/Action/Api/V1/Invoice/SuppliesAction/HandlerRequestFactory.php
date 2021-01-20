<?php

namespace App\Api\Action\Api\V1\Invoice\SuppliesAction;

use App\Clients\Domain\Invoice\UseCase\CreateFromSupplies\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class HandlerRequestFactory
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var MyselfInterface
     */
    private $myself;

    public function __construct(
        RequestStack $requestStack,
        MyselfInterface $myself
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->myself = $myself;
    }

    public function __invoke()
    {
        $handlerRequest = new HandlerRequest();
        $handlerRequest->client = $this->myself->getClient();
        $handlerRequest->items = (array) $this->request->get('items');

        return $handlerRequest;
    }
}
