<?php

namespace Tests\Api\V1\Feedback;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CreateCest
{
    private $basePath = '/api/v1/feedback';

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
    public function testSendPostSuccessfully(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $data = [
            'email' => 'test@email.com',
            'name' => 'John Dou',
            'category' => 'new-card-order',
            'comment' => 'test text',
        ];

        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);
    }

    public function testPostEmptyDataReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendPOST("$this->basePath", []);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'errors' => 'array:!empty',
        ]);

        $I->seeResponseMatchesJsonType([
            'email' => 'array:!empty',
            'name' => 'array:!empty',
            'category' => 'array:!empty',
            'comment' => 'array:!empty',
        ], '$.errors');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendPOST("$this->basePath", []);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /*public function testSendGetReturn405(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'code' => 405,
        ]);
        $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
    }*/
}
