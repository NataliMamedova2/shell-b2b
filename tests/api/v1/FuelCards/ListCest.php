<?php

namespace Tests\Api\V1\FuelCards;

use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ListCest
{
    protected $basePath = '/api/v1/fuel-cards';

    /**
     * Allowed roles.
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testSendGetNoCardsReturnEmptyList(ApiTester $I, Example $example)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($user->getUserName());

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'meta' => [
                'pagination' => [
                    'totalCount' => 'integer',
                    'currentPage' => 'integer',
                ],
                'activeCount' => 'integer',
            ],
            'data' => 'array',
        ]);

        $I->seeResponseEqualsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'activeCount' => 0,
            ],
            'data' => [],
        ]);
    }

    public function testSendGetCardsAnotherClientReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var User $user */
        $user = $I->haveCabinetUser();
        $client = $user->getCompany()->getClient();

        $I->haveFuelCardList(12, ['client1CId' => $client->getClient1CId()]);
        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseEqualsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'activeCount' => 0,
            ],
            'data' => [],
        ]);
    }

    public function testSendGetHaveCardsReturnList(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        $I->haveFuelCardList(15, [
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);
        $I->haveFuelCardList(3, [
            'client1CId' => $client->getClient1CId(),
            'status' => 'blocked',
        ]);
        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'activeCount' => 15,
            ],
        ]);

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'cardNumber' => 'string:!empty',
            'status' => 'string:enum(["active", "blocked"])',
        ], '$.data[0]');
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $user = $I->haveCabinetUser();

        $client = $user->getCompany()->getClient();

        $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    /*   public function testSendPostReturn405(ApiTester $I)
       {
           $I->authorizeAsAdmin();

           $I->sendPOST($this->basePath);

           $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
           $I->seeResponseIsJson();

           $I->seeResponseContainsJson([
               'code' => 405,
           ]);
       }*/
}
