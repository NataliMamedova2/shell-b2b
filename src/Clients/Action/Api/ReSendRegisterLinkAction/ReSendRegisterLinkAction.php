<?php

namespace App\Clients\Action\Api\ReSendRegisterLinkAction;

use App\Clients\Domain\RegisterToken\Register;
use App\Clients\Domain\RegisterToken\Repository\RegisterRepository;
use App\Clients\Domain\RegisterToken\UseCase\Update\Handler;
use App\Clients\Domain\RegisterToken\UseCase\Update\HandlerRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ReSendRegisterLinkAction
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
     * @var RegisterRepository
     */
    private $tokenRepository;

    /**
     * @var Handler
     */
    private $handler;

    public function __construct(
        ValidatorInterface $validator,
        Serializer $serializer,
        RegisterRepository $tokenRepository,
        Handler $handler
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->tokenRepository = $tokenRepository;
        $this->handler = $handler;
    }

    /**
     * @Route(
     *     "/admin/api/v1/company/resend-register-link/{id}",
     *     name="admin_api_company_resend_register_link",
     *     methods={"POST"},
     *     requirements={
     *         "id"="%routing.uuid%"
     *     }
     * )
     *
     * @param Request $request
     *
     * @param string  $id
     *
     * @return Response
     */
    public function __invoke(Request $request, string $id): Response
    {
        $token = $this->tokenRepository->findById($id);

        if (!$token instanceof Register) {
            throw new NotFoundHttpException();
        }

        /** @var HandlerRequest $handlerRequest */
        $handlerRequest = $this->serializer->deserialize($request->getContent(), HandlerRequest::class, 'json');
        $handlerRequest->setId($id);

        $errors = $this->validator->validate($handlerRequest);

        if (count($errors) > 0) {
            $errorList = [];
            foreach ($errors as $error) {
                $errorList[$error->getPropertyPath()] = $error->getMessage();
            }

            $response = $this->serializer->serialize(['errors' => $errorList], 'json');

            return new Response($response, 400, ['Content-Type' => 'application/json']);
        }

        $result = $this->handler->handle($handlerRequest);

        $data = [
            'id' => $result->getId(),
            'email' => $result->getEmail(),
        ];

        $response = $this->serializer->serialize($data, 'json');

        return new Response($response, 200, ['Content-Type' => 'application/json']);
    }
}
