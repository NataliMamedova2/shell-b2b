<?php

namespace App\Api\Crud\Action;

use App\Api\Crud\Interfaces\DataTransformer;
use App\Api\Crud\Interfaces\Response;
use Domain\Interfaces\Handler;
use Domain\Interfaces\HandlerRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CommandAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Response
     */
    private $response;

    public function __construct(
        ValidatorInterface $validator,
        Response $response
    ) {
        $this->validator = $validator;
        $this->response = $response;
    }

    public function __invoke(
        HandlerRequest $handlerRequest,
        Handler $handler,
        ?DataTransformer $dataTransformer
    ): SymfonyResponse {

        if (false === method_exists($handler, 'handle')) {
            throw new \InvalidArgumentException();
        }

        $errors = $this->validator->validate($handlerRequest);

        if (count($errors) > 0) {
            return $this->response->createErrorResponse($errors);
        }

        $result = $handler->handle($handlerRequest);

        if ($dataTransformer instanceof DataTransformer) {
            $result = $dataTransformer->transform($result);
        }

        return $this->response->createSuccessResponse($result);
    }
}
