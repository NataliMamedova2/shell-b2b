<?php

namespace Tests\Api\V1\Discount;

use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ListCest
{
    protected $basePath = '/api/v1/discounts';

    public function testSendGetReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([]);
    }

    public function testSendGetHaveFuelReturnList(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany();

        $I->haveDiscountsList($client->getClient(), 10);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'sum' => 'integer:!empty',
            'date' => 'string:!empty',
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
