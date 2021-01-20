<?php

namespace Tests\Api\V1\Transaction\NetworkStations;

use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class NetworkStationsListCest
{
    protected $basePath = '/api/v1/transactions/network-stations';

    public function testGetTransactionNetworkStationsNoTransactionReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([]);
    }

    public function testGetTransactionNetworkStationsHaveTransactionReturnList(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->authorizeAsAdmin();

        $client = $myself->getCompany()->getClient();

        $azsCode = '00-0000282';
        $azsName = 'АЗС Socar № 102';
        $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'azsCode' => $azsCode,
            'azsName' => $azsName,
        ]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'code' => 'string:!empty',
            'name' => 'string:!empty',
        ], '$.[0]');

        $I->seeResponseEqualsJson([
            [
                'code' => $azsCode,
                'name' => $azsName,
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
