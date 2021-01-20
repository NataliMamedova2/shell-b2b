<?php

namespace Tests\Api\V1\Transaction\Regions;

use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class SuppliesListCest
{
    protected $basePath = '/api/v1/transactions/supplies';

    public function testGetTransactionSuppliesNoTransactionReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([]);
    }

    public function testGetTransactionSuppliesHaveTransactionAndSuppliesReturnList(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->authorizeAsAdmin();

        $client = $myself->getCompany()->getClient();

        $fuelCode = 'КВЦ0000001';
        $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'fuelCode' => $fuelCode,
        ]);

        $fuelFuelType = 1;
        $fuelName = 'Бензин А-95';
        $I->haveFuelType([
            'fuelCode' => $fuelCode,
            'fuelName' => $fuelName,
            'fuelType' => $fuelFuelType,
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
                'code' => $fuelCode,
                'name' => $fuelName,
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
