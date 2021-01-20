<?php

namespace App\Api\Action\Api\V1\FuelCard\CardOrder;

use App\Clients\Domain\CardOrder\UseCase\Create\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

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

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    public function __construct(
        RequestStack $requestStack,
        MyselfInterface $myself,
        DenormalizerInterface $denormalizer
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->myself = $myself;
        $this->denormalizer = $denormalizer;
    }

    public function __invoke()
    {
        $data = $this->request->request->all();

        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->denormalizer->denormalize($data, HandlerRequest::class);
        $handlerRequest->user = $this->myself->get();

        return $handlerRequest;
    }
}
