<?php

namespace App\Api\Action\Api\V1\LastSystemUpdate;

use App\Api\Crud\Interfaces\Response;
use App\Import\Domain\Import\Import;
use App\Import\Domain\Import\ValueObject\Status\DoneStatus;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class ReadAction
{
    /**
     * @var Repository
     */
    private $importRepository;

    /**
     * @var Response
     */
    private $jsonResponse;

    public function __construct(
        Repository $importRepository,
        Response $jsonResponse
    ) {
        $this->importRepository = $importRepository;
        $this->jsonResponse = $jsonResponse;
    }

    public function __invoke(): SymfonyResponse
    {
        $doneStatus = new DoneStatus();

        /** @var Import|null $lastSuccessImport */
        $lastSuccessImport = $this->importRepository->find([
            'status_equalTo' => $doneStatus->getValue(),
        ], ['endedAt' => 'DESC']);

        $data = [
            'dateTime' => ($lastSuccessImport instanceof Import) ? $lastSuccessImport->getEndedAt() : null,
        ];

        return $this->jsonResponse->createSuccessResponse($data);
    }
}
