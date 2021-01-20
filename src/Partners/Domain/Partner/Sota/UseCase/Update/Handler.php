<?php

namespace App\Partners\Domain\Partner\Sota\UseCase\Update;

use App\Security\Partners\MyselfInterface;
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
            $partner = $this->mySelf->getPartner();
            $partner->setSotaToken($handlerRequest->token);

            $this->objectManager->flush();
        }
    }
}
