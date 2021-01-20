<?php

namespace Tests\Api\V1\Search;

use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ServicesListCest
{
    protected $basePath = '/api/v1/services/search';

    public function testGetServicesSuppliesNoSuppliesReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([]);
    }

    public function testGetServicesSuppliesHaveSuppliesWithEmptyPurseCodeReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->haveServicesList(4, ['purseCode' => 0]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([]);
    }

    public function testGetServicesSuppliesHaveGoodsSuppliesReturnList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $serviceFuelType = 3;
        $fuelType_1 = $I->haveFuelType([
            'fuelName' => 'A name',
            'fuelType' => $serviceFuelType,
            'purseCode' => 0,
        ]);
        $fuelType_2 = $I->haveFuelType([
            'fuelName' => 'C name',
            'fuelType' => $serviceFuelType,
            'purseCode' => 12,
        ]);
        $fuelType_3 = $I->haveFuelType([
            'fuelName' => 'D name',
            'fuelType' => $serviceFuelType,
            'purseCode' => 13,
        ]);
        $fuelType_4 = $I->haveFuelType([
            'fuelName' => 'B name',
            'fuelType' => $serviceFuelType,
            'purseCode' => 14,
        ]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'name' => 'string:!empty',
        ]);

        $I->seeResponseEqualsJson([
            [
                'id' => $fuelType_4->getId(),
                'name' => $fuelType_4->getFuelName(),
            ],
            [
                'id' => $fuelType_2->getId(),
                'name' => $fuelType_2->getFuelName(),
            ],
            [
                'id' => $fuelType_3->getId(),
                'name' => $fuelType_3->getFuelName(),
            ],
        ]);

        $I->dontSeeResponseContainsJson([
            'name' => $fuelType_1->getFuelName(),
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
