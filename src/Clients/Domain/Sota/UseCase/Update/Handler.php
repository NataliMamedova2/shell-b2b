<?php

namespace App\Clients\Domain\Sota\UseCase\Update;

use App\Security\Cabinet\MyselfInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\Interfaces\Handler as DomainHandler;

final class Handler implements DomainHandler
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var MyselfInterface */
    private $mySelf;

    /**
     * Handler constructor.
     * @param ObjectManager $objectManager
     * @param MyselfInterface $mySelf
     */
    public function __construct(ObjectManager $objectManager, MyselfInterface $mySelf)
    {
        $this->objectManager = $objectManager;
        $this->mySelf = $mySelf;
    }

    /**
     * @param HandlerRequest $handlerRequest
     */
    public function handle(HandlerRequest $handlerRequest): void
    {
        if (false === empty($handlerRequest->token)) {
            $client = $this->mySelf->getClient();
            $client->setSotaToken($handlerRequest->token);

            $this->objectManager->flush();
        }
    }
}
