<?php

namespace App\Clients\Action\Backend\Transaction\CreateReportAction;

use App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service\TransactionsXlsWriter;
use App\Api\Crud\Interfaces\QueryHandler;
use App\Clients\Action\Backend\Transaction\ListAction\QueryRequest as ListQueryRequest;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CreateReportAction
{
    /**
     * @var TransactionsXlsWriter
     */
    private $excelService;

    /**
     * @var Repository
     */
    private $clientRepository;

    public function __construct(
        TransactionsXlsWriter $excelService,
        Repository $clientRepository
    ) {
        $this->excelService = $excelService;
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(
        ListQueryRequest $listQueryRequest,
        QueryHandler $reportTransactionsHandler
    ): StreamedResponse {
        $queryRequest = new QueryRequestAdapter($listQueryRequest);
        $result = $reportTransactionsHandler->handle($queryRequest);

        $transactions = $result['transactions'] ?? [];
        $replenishmentTransactions = $result['replenishmentTransactions'] ?? [];
        $fuelTypes = $result['fuelTypes'] ?? [];
        $queryParams = $queryRequest->getQueryParams();

        $client1CId = [];

        foreach ($transactions as $transaction) {
            $client1CId[$transaction->getClient1CId()] = $transaction->getClient1CId();
        }

        foreach ($replenishmentTransactions as $transaction) {
            $client1CId[$transaction->getClient1CId()] = $transaction->getClient1CId();
        }

        $company = '';
        $companies = [];
        if (false === empty($queryRequest->getQueryParams()['clientName'])) {
            $clients = $this->clientRepository->findMany([
                'client1CId_in' => array_values($client1CId),
            ]);
            if (1 === \count($clients)) {
                $company = $clients[0]->getFullName();
            }
        }
        $clients = $this->clientRepository->findMany([
            'client1CId_in' => array_values($client1CId),
        ]);
        foreach ($clients as $client) {
            $companies[$client->getClient1CId()] = $client->getFullName();
        }

        $writer = $this->excelService->create(
            $transactions,
            $replenishmentTransactions,
            $fuelTypes,
            $company,
            $queryParams,
            $companies
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
