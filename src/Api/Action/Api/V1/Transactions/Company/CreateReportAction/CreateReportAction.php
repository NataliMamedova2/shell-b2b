<?php

namespace App\Api\Action\Api\V1\Transactions\Company\CreateReportAction;

use App\Api\Action\Api\V1\Transactions\Company\CreateReportAction\Service\CreateExcelService;
use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Security\Cabinet\MyselfInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CreateReportAction
{
    /** @var MyselfInterface */
    private $mySelf;

    /** @var CreateExcelService */
    private $excelService;

    public function __construct(CreateExcelService $excelService, MyselfInterface $mySelf)
    {
        $this->mySelf = $mySelf;
        $this->excelService = $excelService;
    }

    public function __invoke(
        QueryRequest $queryRequest,
        QueryHandler $reportHandler
    ): StreamedResponse {
        $transactions = $reportHandler->handle($queryRequest)['transactions'] ?? [];

        $company = $this->mySelf->getClient()->getFullName();

        $writer = $this->excelService->create($transactions, $company, $queryRequest->getQueryParams());

        $response = new StreamedResponse(
            static function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="export-transactions.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
