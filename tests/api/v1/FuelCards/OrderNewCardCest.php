<?php

namespace Tests\Api\V1\FuelCards;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class OrderNewCardCest
{
    protected $basePath = '/api/v1/fuel-cards/order-new-card';

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
    public function testSendPostSaveToDbReturnTrue(ApiTester $I, Example $example)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($user->getUserName());

        $data = [
            'count' => 2,
            'name' => 'test name',
            'phone' => '+380976544331',
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
        ]);

        $I->seeInDatabase('cards_order', [
            'user_id' => $user->getId(),
            'count' => $data['count'],
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);
    }

    public function testSendPostEmptyDataReturnErrors(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'count' => 'array:!empty',
            'name' => 'array:!empty',
            'phone' => 'array:!empty',
        ], '$.errors');
    }

    public function testSendPostUnauthorizedReturnError(ApiTester $I)
    {
        $data = [
            'count' => 2,
            'name' => 'test name',
            'phone' => '+380976544331',
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $user = $I->haveCabinetUser();

        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);

        $I->sendPOST($this->basePath);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
