<?php

namespace Tests\Api\V1\Transaction\Regions;

use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class RegionsListCest
{
    protected $basePath = '/api/v1/transactions/regions';

    public function testGetTransactionRegionsNoTransactionReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([]);
    }

    public function testGetTransactionRegionsHaveTransactionReturnList(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->authorizeAsAdmin();

        $client = $myself->getCompany()->getClient();

        $regionCode = 'КВЦ0000001';
        $regionName = 'Чернігівська обл.';
        $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'regionCode' => $regionCode,
            'regionName' => $regionName,
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
                'code' => $regionCode,
                'name' => $regionName,
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
