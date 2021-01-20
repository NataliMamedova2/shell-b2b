<?php

namespace App\Api\Action\Api\V1\Drivers;

use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\UseCase\ChangeStatus\HandlerRequest;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ChangeStatusHandlerRequestFactory
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var MyselfInterface
     */
    private $myself;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        RequestStack $requestStack,
        MyselfInterface $myself,
        Repository $repository
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->myself = $myself;
        $this->repository = $repository;
    }

    public function __invoke(): HandlerRequest
    {
        $id = $this->request->get('id');

        $driverId = DriverId::fromString($id);
        $client = $this->myself->getClient();

        $driver = $this->repository->find([
            'id_equalTo' => $driverId->getId(),
            'client1CId_equalTo' => $client->getClient1CId(),
        ]);
        if (!$driver instanceof Driver) {
            throw new NotFoundHttpException('Driver not found');
        }

        $handlerRequest = new HandlerRequest($driverId);
        $handlerRequest->status = $this->request->get('status');

        return $handlerRequest;
    }
}
