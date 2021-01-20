<?php

namespace Tests\Api\V1\Dashboard;

use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;
use Tests\Helper\Fixtures;

final class InfoActionCest
{
    protected $basePath = '/api/v1/dashboard';

    /**
     * @var Fixtures
     */
    protected $fixtures;

    public function testSendGetNoData(ApiTester $I)
    {
        $myself = $I->haveCabinetUser();
        $I->authorize($myself->getUserName());

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->seeResponseMatchesJsonType([
            'balance' => 'array|null',
            'balanceUpdate' => 'array|null',
            'creditLimit' => 'integer',
            'availableBalance' => 'integer',
            'lastMonthDiscountSum' => 'integer',
            'cardsStatistic' => 'array|null',
        ]);
    }

    public function testGetDashboardWithClientInfoData(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->haveCabinetUser();
        $I->authorize($myself->getUserName());

        $client = $myself->getCompany()->getClient();

        /** @var ClientInfo $clientInfo */
        $clientInfo = $I->haveClientInfo($client, [
            'balance' => 112300,
            'creditLimit' => 98600,
        ]);
        /** @var RefillBalance $refillBalance */
        $refillBalance = $I->haveRefillBalance($client);
        $I->haveCardTransaction(['client1CId' => $client->getClient1CId()]);

        $previousDiscountSum = 100;
        $date = new \DateTimeImmutable('-1 month');
        $I->haveDiscount($client, ['sum' => $previousDiscountSum, 'date' => $date->format('Y-m-d H:i:s')]);

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->seeResponseMatchesJsonType([
            'balance' => 'array|null',
            'balanceUpdate' => 'array|null',
            'creditLimit' => 'integer',
            'availableBalance' => 'integer',
            'lastMonthDiscountSum' => 'integer',
            'cardsStatistic' => 'array|null',
        ]);

        $I->seeResponseMatchesJsonType([
            'day' => 'integer',
            'week' => 'integer',
            'month' => 'integer',
        ], '$.cardsStatistic');

        $I->seeResponseContainsJson([
            'balance' => [
                'value' => $clientInfo->getBalance(),
            ],
            'balanceUpdate' => [
                'balance' => [
                    'value' => $refillBalance->getAmount(),
                ],
            ],
            'creditLimit' => $clientInfo->getCreditLimit(),
            'lastMonthDiscountSum' => $previousDiscountSum,
        ]);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET("$this->basePath");

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
