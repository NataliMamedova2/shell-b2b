<?php

namespace App\Api\Action\Api\V1\Feedback;

use App\Api\Crud\Interfaces\Response;
use App\Feedback\Domain\Feedback\UseCase\Create\Handler;
use App\Feedback\Domain\Feedback\UseCase\Create\HandlerRequest;
use App\Security\Cabinet\Myself;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class CreateAction
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var Myself
     */
    private $myself;

    /**
     * @var Response
     */
    private $response;

    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        Handler $handler,
        Myself $myself,
        Response $response
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->handler = $handler;
        $this->myself = $myself;
        $this->response = $response;
    }

    /**
     * @Route(
     *     "/api/v1/feedback",
     *     name="api_v1_feedback",
     *     methods={"POST"}
     * )
     *
     * * @param Request $request
     *
     * @return SymfonyResponse
     *
     * @throws \Exception
     */
    public function __invoke(Request $request): SymfonyResponse
    {
        if (empty($request->getContent())) {
            return $this->response->createErrorResponse(['No data found']);
        }

        /** @var HandlerRequest $payload */
        $payload = $this->serializer->deserialize($request->getContent(), HandlerRequest::class, 'json');
        $payload->user = $this->myself->get();

        $errors = $this->validator->validate($payload);
        if (count($errors)) {
            return $this->response->createErrorResponse($errors);
        }

        $this->handler->handle($payload);

        $data = [
            'success' => true,
        ];

        return $this->response->createSuccessResponse($data);
    }
}
