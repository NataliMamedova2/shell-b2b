<?php

namespace Tests\Api\V1\Transaction;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CompanyTransactionsListCest
{
    protected $basePath = '/api/v1/transactions/company';

    /**
     * Allowed roles.
     *
     * @param ApiTester $I
     * @param Example $example
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testGetCompanyTransactionsReturnEmptyList(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'meta' => [
                'pagination' => [
                    'totalCount' => 'integer',
                    'currentPage' => 'integer',
                ],
                'accountBalance' => 'array|null',
            ],
            'data' => 'array',
        ]);

        $I->seeResponseEqualsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'accountBalance' => null,
            ],
            'data' => [],
        ]);
    }

    public function testGetCompanyTransactionsReturnTransactions(ApiTester $I)
    {
        $myself = $I->authorizeAsAdmin();

        $client = $myself->getCompany()->getClient();

        $I->haveClientInfo($client, ['balance' => -123.983]);

        $returnType = 1;
        $todayTransaction_1 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'type' => $returnType,
        ]);
        $writeOffType = 0;
        $todayTransaction_2 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'type' => $writeOffType,
        ]);
        $returnTransactionDebit_1 = 10000;
        $transactionReturn_1 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'debit' => $returnTransactionDebit_1,
            'type' => $returnType,
            'postDate' => new \DateTimeImmutable('yesterday'),
        ]);
        $returnTransactionDebit_2 = 10000;
        $transactionReturn_2 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'debit' => $returnTransactionDebit_2,
            'type' => $returnType,
            'postDate' => new \DateTimeImmutable('yesterday'),
        ]);
        $writeOffTransactionDebit = 20000;
        $transactionWriteOff = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'debit' => $writeOffTransactionDebit,
            'type' => $writeOffType,
            'postDate' => new \DateTimeImmutable('yesterday'),
        ]);
        $replenishmentWriteOffType = 2;
        $transactionReplenishment = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'debit' => $writeOffTransactionDebit,
            'type' => $replenishmentWriteOffType,
            'postDate' => new \DateTimeImmutable('yesterday'),
        ]);
        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'accountBalance' => [
                    'value' => 12398,
                    'sign' => '-',
                ],
            ],
            'data' => [
                [
                    'amount' => $writeOffTransactionDebit - ($returnTransactionDebit_1 + $returnTransactionDebit_2),
                    'type' => 'write-off-cards',
                ],
            ],
        ]);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
