<?php

namespace App\Clients\Action\Backend\Client\DocumentAction;

use App\Clients\Domain\Client\Client;
use CrudBundle\Action\Response;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DocumentAction
{
    /**
     * @var Repository
     */
    private $clientRepository;

    public function __construct(
        Repository $clientRepository
    ) {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(string $id): Response
    {
        $client = $this->clientRepository->findById($id);

        if (!$client instanceof Client) {
            throw new NotFoundHttpException();
        }

        $result = [
            'client' => $client,
        ];

        return new Response([
            'result' => $result,
        ]);
    }
}
