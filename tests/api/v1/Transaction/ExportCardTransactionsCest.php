<?php

namespace Tests\Api\V1\Transaction;

use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Transaction\Card\Transaction;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ExportCardTransactionsCest
{
    protected $basePath = '/api/v1/transactions/card/report';

    /**
     * Allowed roles.
     *
     * @param Example $example
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    /* public function testGetCardTransactionsReturnEmptyList(ApiTester $I, Example $example)
     {
         $myself = $I->haveCabinetUser(['role' => $example['role']]);
         $I->authorize($myself->getUserName());

         $I->sendGET($this->basePath);

         $I->seeResponseCodeIs(HttpCode::OK);
         $I->seeHttpHeader('Content-Type', 'application/vnd.ms-excel');
         $I->seeHttpHeader('Content-Disposition', 'attachment;filename="export-transactions.xls"');
     }*/

    /*public function testGetCardTransactionsReturnTransactions(ApiTester $I)
    {
        $myself = $I->authorizeAsAdmin();

        $client = $myself->getCompany()->getClient();


        $fuel = $I->haveFuelType(['fuelType' => 1]);


        $transaction_1 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'fuelCode' => $fuel->getFuelCode(),
            'type' => 0,
        ]);

        $transaction_2 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'fuelCode' => $fuel->getFuelCode(),
            'type' => 1,
        ]);

        $transaction_3 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'fuelCode' => $fuel->getFuelCode(),
            'type' => 2,
        ]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeHttpHeader('Content-Type', 'application/vnd.ms-excel');
        $I->seeHttpHeader('Content-Disposition', 'attachment;filename="export-transactions.xls"');
    }*/

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
