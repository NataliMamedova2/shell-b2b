<?php

namespace Tests\Api\V1\Search;

use App\Clients\Domain\Fuel\Type\Type;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class SuppliesListCest
{
    protected $basePath = '/api/v1/supplies';

    public function testSendGetNoPriceAndFuelsReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([]);
    }

    public function testSendGetHaveFuelWithoutPriceReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->haveFuelList(10);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([]);
    }

    /**
     * Allowed roles.
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testSendGetHaveFuelWithPriceReturnList(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        /** @var Type[] $fuelTypes */
        $fuelTypes = $I->haveFuelList(10);

        foreach ($fuelTypes as $fuel) {
            $I->haveFuelPrice($fuel);
        }

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'name' => 'string:!empty',
            'price' => 'integer',
        ]);
        $I->dontSeeResponseContainsJson([
            'price' => 0,
        ]);

        foreach ($fuelTypes as $fuel) {
            $I->seeResponseContainsJson([
                'name' => $fuel->getFuelName(),
            ]);
        }
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    /*  public function testSendPostReturn405(ApiTester $I)
      {
          $I->sendPOST($this->basePath);

          $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
          $I->seeResponseIsJson();

          $I->seeResponseContainsJson([
              'code' => 405,
          ]);
      }*/
}
