<?php

namespace Tests\Api\V1\Drivers;

use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CreateCest
{
    protected $basePath = '/api/v1/drivers/create';

    public function testCreateDriverReturnDriver(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $email = uniqid().'@example.com';
        $data = [
            'firstName' => 'John',
            'lastName' => 'Smith',
            'middleName' => 'Dou',
            'phones' => [
                ['number' => '+380972342344'],
                ['number' => '+380992349999'],
            ],
            'carsNumbers' => [
                ['number' => 'AA1233222'],
            ],
            'email' => $email,
            'status' => 'active',
            'note' => 'Note text',
        ];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson($data);

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'email' => 'string:email',
            'firstName' => 'string',
            'lastName' => 'string',
            'middleName' => 'string',
            'phones' => 'array|!empty',
            'carsNumbers' => 'array|empty',
            'status' => 'string:enum(["active", "blocked"])',
            'note' => 'string|empty',
        ]);

        $I->seeInDatabase('drivers', ['email' => $email]);
    }

    public function testCreateDriverEmptyDataReturnErrors(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'firstName' => 'array',
            'lastName' => 'array',
            'phones' => 'array',
        ], '$.errors');
    }

    public function testCreateDriverUnauthorizedReturn401(ApiTester $I)
    {
        $email = uniqid().'@example.com';
        $data = [
            'firstName' => 'John',
            'lastName' => 'Smith',
            'middleName' => 'Dou',
            'phones' => [
                ['number' => '+380972342344'],
                ['number' => '+380992349999'],
            ],
            'carsNumbers' => [
                ['number' => 'AA1233222'],
            ],
            'email' => $email,
            'status' => 'active',
            'note' => 'Note text',
        ];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }
}
