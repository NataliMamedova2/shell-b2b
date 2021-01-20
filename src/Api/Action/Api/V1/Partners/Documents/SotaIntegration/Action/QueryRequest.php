<?php

namespace App\Api\Action\Api\V1\Partners\Documents\SotaIntegration\Action;

use App\Api\Crud\Interfaces\Response as JsonResponse;
use App\Partners\Domain\Partner\Sota\UseCase\Update\Handler;
use App\Partners\Domain\Partner\Sota\UseCase\Update\HandlerRequest;
use App\Security\Partners\MyselfInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class QueryRequest
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
     * QueryRequest constructor.
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
     *     "/api/v1/partners/sota",
     *     name="api_v1_partners_sota",
     *     methods={"GET"}
     * )
     *
     * @throws \Exception
     */
    public function __invoke(): Response
    {
        try {
            $partner = $this->mySelf->getPartner();

            if (null === $partner->getEdrpou()) {
                throw new \Exception('permission denied');
            }

            $token = $partner->getSotaToken();
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
