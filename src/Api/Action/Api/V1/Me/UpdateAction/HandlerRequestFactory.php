<?php

namespace App\Api\Action\Api\V1\Me\UpdateAction;

use App\Clients\Domain\User\UseCase\UpdateProfile\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class HandlerRequestFactory
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var MyselfInterface
     */
    private $myself;

    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    public function __construct(
        RequestStack $requestStack,
        MyselfInterface $myself
    ) {
        $this->requestStack = $requestStack;
        $this->myself = $myself;
        $this->normalizer = new ObjectNormalizer();
    }

    public function __invoke(): HandlerRequest
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $myself = $this->myself->get();

        $handlerRequest = new HandlerRequest($myself);
        $context = [
            AbstractNormalizer::OBJECT_TO_POPULATE => $handlerRequest,
        ];
        $data = $request->request->all();

        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->normalizer->denormalize($data, HandlerRequest::class, null, $context);

        return $handlerRequest;
    }
}
