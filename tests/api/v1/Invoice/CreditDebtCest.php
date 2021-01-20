<?php

namespace Tests\Api\V1\Invoice;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CreditDebtCest
{
    protected $basePath = '/api/v1/invoice/credit-debt';

    public function testCreditDebtNoClientInfoReturnZero(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([
            'amount' => 0,
        ]);
    }

    public function testCreditDebtHavePositiveBalanceReturnZero(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        $client = $user->getCompany()->getClient();

        $I->haveClientInfo($client, ['balance' => 200]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([
            'amount' => 0,
        ]);
    }

    public function testCreditDebtHaveZeroBalanceReturnZero(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        $client = $user->getCompany()->getClient();

        $I->haveClientInfo($client, ['balance' => 0]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([
            'amount' => 0,
        ]);
    }

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
    public function testCreditDebtHaveNegativeBalanceReturnAbsoluteAmount(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $client = $myself->getCompany()->getClient();

        $absoluteBalance = 10;
        $I->haveClientInfo($client, ['balance' => -$absoluteBalance]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([
            'amount' => $absoluteBalance * 100,
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
