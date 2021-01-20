<?php

namespace App\Api\Action\Api\V1\Drivers;

use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\UseCase\Update\HandlerRequest;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UpdateHandlerRequestFactory
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

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    public function __construct(
        RequestStack $requestStack,
        MyselfInterface $myself,
        Repository $repository,
        DenormalizerInterface $denormalizer
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->myself = $myself;
        $this->repository = $repository;
        $this->denormalizer = $denormalizer;
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

        $data = $this->request->request->all();
        $handlerRequest = new HandlerRequest($driverId);
        $context = [
            AbstractObjectNormalizer::OBJECT_TO_POPULATE => $handlerRequest,
        ];

        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->denormalizer->denormalize($data, HandlerRequest::class, null, $context);

        return $handlerRequest;
    }
}
