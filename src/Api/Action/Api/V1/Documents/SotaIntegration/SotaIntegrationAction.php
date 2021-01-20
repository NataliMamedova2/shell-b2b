<?php

namespace App\Api\Action\Api\V1\Documents\SotaIntegration;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Clients\Domain\Sota\UseCase\Update\Handler;
use App\Clients\Domain\Sota\UseCase\Update\HandlerRequest;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SotaIntegrationAction
{
    /** @var MyselfInterface */
    private $mySelf;

    /**
     * @var JsonResponse
     */
    private $jsonResponse;

    /** @var Handler */
    private $handler;

    /**
     * SotaIntegrationAction constructor.
     * @param MyselfInterface $mySelf
     * @param JsonResponse $jsonResponse
     * @param Handler $handler
     */
    public function __construct(MyselfInterface $mySelf, JsonResponse $jsonResponse, Handler $handler)
    {
        $this->mySelf = $mySelf;
        $this->jsonResponse = $jsonResponse;
        $this->handler = $handler;
    }

    /**
     * @Route(
     *     "/api/v1/sota",
     *     name="api_v1_sota",
     *     methods={"GET"}
     * )
     *
     * @throws \Exception
     */
    public function __invoke(): Response
    {
        try {
            $client = $this->mySelf->getClient();

            if (null === $client->getEdrpouInn()) {
                throw new \Exception('permission denied');
            }

            $token = $client->getSotaToken();
            if (null === $token) {
                $token = \bin2hex(\random_bytes(16));

                $handlerRequest = new HandlerRequest();
                $handlerRequest->token = $token;
                $this->handler->handle($handlerRequest);
            }
        } catch (\Throwable $exception) {
            return $this->jsonResponse->createErrorResponse($exception->getMessage());
        }

        return $this->jsonResponse->createSuccessResponse(['token' => $token]);
    }
}
