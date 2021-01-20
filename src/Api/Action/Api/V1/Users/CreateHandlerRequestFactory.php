<?php

namespace App\Api\Action\Api\V1\Users;

use App\Clients\Domain\User\UseCase\Create\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CreateHandlerRequestFactory
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

    public function __construct(RequestStack $requestStack, MyselfInterface $myself)
    {
        $this->requestStack = $requestStack;
        $this->myself = $myself;
        $this->normalizer = new ObjectNormalizer();
    }

    public function __invoke(): HandlerRequest
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new NotFoundHttpException('Request not found');
        }

        $data = $request->request->all();

        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->normalizer->denormalize($data, HandlerRequest::class);
        $handlerRequest->company = $this->myself->get()->getCompany();

        return $handlerRequest;
    }
}
