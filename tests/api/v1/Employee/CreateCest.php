<?php

namespace Tests\Api\V1\Employee;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CreateCest
{
    protected $basePath = '/api/v1/company/employees/create';

    public function testSendPostReturnEmployer(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'admin',
            'password' => 'drowssaP123',
        ];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        unset($data['password']);
        $I->seeResponseContainsJson($data);

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'username' => 'string:!empty',
            'email' => 'string:email:!empty',
            'firstName' => 'string:!empty',
            'middleName' => 'string|empty',
            'lastName' => 'string:!empty',
            'phone' => 'string|empty',
            'status' => 'string:enum(["active", "blocked"])',
            'role' => 'string:enum(["admin", "manager", "accountant"])',
            'createdAt' => 'string:date',
            'lastLoggedAt' => 'string|empty',
        ]);
        $I->dontSeeResponseJsonMatchesJsonPath('$.password');

        $I->seeInDatabase('company_users', ['username' => $data['username']]);
    }

    public function testSendPostEmptyDataReturnErrors(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'firstName' => 'array',
            'lastName' => 'array',
            'email' => 'array',
            'username' => 'array',
            'password' => 'array',
            'role' => 'array',
        ], '$.errors');
    }

    public function testSendPostEmailExistReturnErrors(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var \App\Clients\Domain\User\User $user */
        $user = $I->haveCabinetUser();

        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => $user->getEmail(),
            'role' => 'admin',
            'password' => 'drowssaP123',
        ];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'email' => 'array',
        ], '$.errors');
    }

    public function testSendPostUsernameExistReturnErrors(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var \App\Clients\Domain\User\User $user */
        $user = $I->haveCabinetUser();

        $data = [
            'username' => $user->getUsername(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'admin',
            'password' => 'drowssaP123',
        ];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'username' => 'array',
        ], '$.errors');
    }

    public function testSendPostUsernameEmailExistReturnErrors(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var \App\Clients\Domain\User\User $user */
        $user = $I->haveCabinetUser();

        $data = [
            'username' => $user->getUsername(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => $user->getEmail(),
            'role' => 'admin',
            'password' => 'drowssaP123',
        ];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'username' => 'array',
            'email' => 'array',
        ], '$.errors');
    }

    /**
     * @param ApiTester $I
     * @param Example $example
     *
     * @throws \Exception
     *
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testForbiddenRoles(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'admin',
            'password' => 'drowssaP123',
        ];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }

    public function testSendPostUnauthorizedReturnEmployer(ApiTester $I)
    {
        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'admin',
            'password' => 'drowssaP123',
        ];
        $I->sendPOST("$this->basePath", $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

  /*  public function testSendGetReturn405(ApiTester $I)
    {
        $I->sendGET("$this->basePath");

        $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 405,
        ]);
    }*/
}
