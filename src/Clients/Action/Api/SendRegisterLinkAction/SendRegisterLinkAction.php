<?php

namespace App\Clients\Action\Api\SendRegisterLinkAction;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\RegisterToken\UseCase\Create\Handler;
use App\Clients\Domain\RegisterToken\UseCase\Create\HandlerRequest;
use App\Users\Domain\User\User;
use App\Users\Infrastructure\Criteria\Manager1C;
use App\Users\Infrastructure\Criteria\ManagerForClient;
use App\Users\Infrastructure\Repository\UserRepository;
use Domain\Exception\EntityNotFoundException;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SendRegisterLinkAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Handler
     */
    private $handler;

    public function __construct(
        ValidatorInterface $validator,
        Serializer $serializer,
        Repository $repository,
        UserRepository $userRepository,
        Handler $handler
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->handler = $handler;
    }

    /**
     * @Route(
     *     "/admin/api/v1/company/{clientId}/send-register-link",
     *     name="admin_api_company_send_register_link",
     *     methods={"POST"},
     *     requirements={
     *          "clientId"="%routing.uuid%"
     *     }
     * )
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->serializer->deserialize($request->getContent(), HandlerRequest::class, 'json');
        $handlerRequest->client = $request->get('clientId');

        $errors = $this->validator->validate($handlerRequest);

        if (count($errors) > 0) {
            $errorList = [];
            foreach ($errors as $error) {
                $errorList[$error->getPropertyPath()] = $error->getMessage();
            }

            $response = $this->serializer->serialize(['errors' => $errorList], 'json');

            return new Response($response, 400, ['Content-Type' => 'application/json']);
        }

        /** @var Client $client */
        $client = $this->repository->findById($request->get('clientId'));

        $handlerRequest->client = $client;

        $result = $this->handler->handle($handlerRequest);

        $data = [
            'id' => $result->getId(),
            'email' => $result->getEmail(),
        ];

        $response = $this->serializer->serialize($data, 'json');

        return new Response($response, 200, ['Content-Type' => 'application/json']);
    }
}
