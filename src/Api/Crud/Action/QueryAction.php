<?php

namespace App\Api\Crud\Action;

use App\Api\Crud\Interfaces\DataTransformer;
use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Api\Crud\Interfaces\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class QueryAction
{
    /**
     * @var Response
     */
    private $response;

    public function __construct(
        Response $response
    ) {
        $this->response = $response;
    }

    public function __invoke(
        QueryRequest $queryRequest,
        QueryHandler $queryHandler,
        ?DataTransformer $dataTransformer
    ): SymfonyResponse {

        $result = $queryHandler->handle($queryRequest);

        if ($dataTransformer instanceof DataTransformer) {
            $result = $dataTransformer->transform($result);
        }

        return $this->response->createSuccessResponse($result);
    }
}
