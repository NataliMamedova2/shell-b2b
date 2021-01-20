<?php

namespace App\Clients\Action\Backend\Client\UpdateProfileAction;

use App\Clients\Domain\Client\UseCase\UpdateProfile\HandlerRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UpdateProfileHandlerRequestFactory
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    public function __construct(
        RequestStack $requestStack,
        DenormalizerInterface $denormalizer
    ) {
        $request = $requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->denormalizer = $denormalizer;
    }

    public function __invoke(): HandlerRequest
    {
        $data = $this->request->request->all();

        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->denormalizer->denormalize($data, HandlerRequest::class);

        $userId = $this->request->get('id');

        $handlerRequest->setId($userId);

        return $handlerRequest;
    }
}
