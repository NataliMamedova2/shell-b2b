<?php

namespace App\Clients\Action\Backend\Client\DocumentAction;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Document\UseCase\UploadDocument\Handler;
use App\Clients\Domain\Document\UseCase\UploadDocument\HandlerRequest;
use App\Clients\Domain\Document\Service\UploadDocumentFileService;
use App\Clients\Domain\Document\ValueObject\Type;
use CrudBundle\Action\Response;
use CrudBundle\Interfaces\RedirectResponse;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UploadAction
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Repository
     */
    private $clientRepository;
    /**
     * @var UploadDocumentFileService
     */
    private $uploadDocumentFileService;
    /**
     * @var Handler
     */
    private $handler;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        RequestStack $requestStack,
        Repository $clientRepository,
        UploadDocumentFileService $uploadDocumentFileService,
        Handler $handler,
        ValidatorInterface $validator,
        FlashBagInterface $flashBag
    ) {
        $request = $requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $this->request = $request;
        $this->clientRepository = $clientRepository;
        $this->handler = $handler;
        $this->validator = $validator;
        $this->flashBag = $flashBag;
        $this->uploadDocumentFileService = $uploadDocumentFileService;
    }

    public function __invoke(string $id, RedirectResponse $redirectResponse)
    {
        $client = $this->clientRepository->findById($id);

        if (!$client instanceof Client) {
            throw new NotFoundHttpException();
        }

        $document = new DocumentFormDto();
        $document->type = $this->request->get('type');
        $document->document = $this->request->files->get('document');

        $result = [
            'client' => $client,
        ];

        $errors = $this->validator->validate($document);
        if (count($errors) > 0) {
            $this->flashBag->set('error', 'save_data.error');

            return new Response([
                'data' => $document,
                'result' => $result,
                'errors' => $errors,
            ]);
        }

        $uploadedDocument = $document->document;
        $resource = fopen($uploadedDocument->getRealPath(), 'rb');
        $prefixName = sha1($uploadedDocument->getClientOriginalName());
        $extension = $uploadedDocument->getClientOriginalExtension();

        $file = $this->uploadDocumentFileService->upload(
            $resource,
            $prefixName,
            $extension
        );

        $request = new HandlerRequest($client, $file, Type::fromName($document->type));

        $this->handler->handle($request);

        $this->flashBag->set('success', 'save_data.success');

        $response = new Response([
            'result' => $client,
        ]);

        return $redirectResponse->redirect($response);
    }
}
