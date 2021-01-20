<?php

namespace App\Api\Action\Api\V1\Documents\ActCheckingAction;

use App\Clients\Domain\Document\UseCase\ActChecking\HandlerRequest;
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

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->myself = $myself;
    }

    public function __invoke()
    {
        return new HandlerRequest(
            $this->myself->getClient(),
            (string) $this->request->get('dateFrom'),
            (string) $this->request->get('dateTo')
        );
    }
}
