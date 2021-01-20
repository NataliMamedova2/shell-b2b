<?php

namespace App\Api\Action\Api\V1\Transactions\Card\CreateReportAction;

use App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service\TransactionsXlsWriter;
use App\Security\Cabinet\MyselfInterface;
use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CreateReportAction
{
    /**
     * @var TransactionsXlsWriter
     */
    private $excelService;

    /**
     * @var MyselfInterface
     */
    private $myself;

    public function __construct(
        TransactionsXlsWriter $excelService,
        MyselfInterface $myself
    ) {
        $this->excelService = $excelService;
        $this->myself = $myself;
    }

    public function __invoke(
        QueryRequest $queryRequest,
        QueryHandler $reportTransactionsHandler
    ): StreamedResponse {

        $result = $reportTransactionsHandler->handle($queryRequest);

        $transactions = $result['transactions'] ?? [];
        $replenishmentTransactions = $result['replenishmentTransactions'] ?? [];
        $fuelTypes = $result['fuelTypes'] ?? [];
        $company = $this->myself->getClient()->getFullName();

        $writer = $this->excelService->create(
            $transactions,
            $replenishmentTransactions,
            $fuelTypes,
            $company,
            $queryRequest->getQueryParams()
        );

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
