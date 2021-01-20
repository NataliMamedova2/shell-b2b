<?php

namespace Tests\Api\V1\Me;

use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class UpdateCest
{
    private $basePath = '/api/v1/me/update';

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
    public function testUpdateProfileEmptyMiddleNameReturnUser(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $data = [
            'username' => 'johndou',
            'email' => 'test@emil.com',
            'firstName' => 'John',
            'middleName' => '',
            'lastName' => 'Dou',
            'phone' => '+380963332211',
            'status' => 'blocked',
            'role' => 'admin',
        ];

        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'username' => $data['username'],
            'email' => $data['email'],
            'firstName' => $data['firstName'],
            'role' => $myself->getRoleName(),
            'status' => $myself->getStatusName(),
        ]);
        $I->dontSeeResponseJsonMatchesJsonPath('$.password');
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @param ApiTester $I
     * @param Example $example
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testUpdateProfileEmptyDataReturnError(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $I->sendPOST("$this->basePath", []);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'username' => 'array:!empty',
            'email' => 'array:!empty',
        ], '$.errors');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testUpdateProfileNoChangeUsernameEmailDataReturnUser(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->haveCabinetUser();
        $I->authorize($myself->getUserName());

        $data = [
            'username' => $myself->getUsername(),
            'email' => $myself->getEmail(),
            'firstName' => $myself->getName()->getFirstName(),
            'middleName' => $myself->getName()->getMiddleName(),
            'lastName' => $myself->getName()->getLastName(),
            'phone' => $myself->getPhone(),
        ];

        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'username' => $data['username'],
            'email' => $data['email'],
            'firstName' => $data['firstName'],
            'middleName' => $data['middleName'],
            'role' => $myself->getRoleName(),
            'status' => $myself->getStatusName(),
        ]);
        $I->dontSeeResponseJsonMatchesJsonPath('$.password');
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendPOST("$this->basePath", []);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
