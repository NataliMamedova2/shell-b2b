<?php

namespace Tests\Api\V1\Employee;

use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class UpdateCest
{
    protected $basePath = '/api/v1/company/employees/update/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testSendPostUpdateUserReturnUser(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        /** @var User $user */
        $user = $I->haveCabinetUser([], $user->getCompany());

        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'accountant',
            'password' => 'drowssaP123',
        ];

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), $data);

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
        $user = $I->authorizeAsAdmin();

        /** @var User $user */
        $user = $I->haveCabinetUser([], $user->getCompany());

        $data = [];

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'firstName' => 'array',
            'lastName' => 'array',
            'email' => 'array',
            'username' => 'array',
            'role' => 'array',
        ], '$.errors');
    }

    public function testSendPostUpdateMyselfReturnNotFound(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'accountant',
        ];

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendPostUserAnotherCompanyReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $company = $I->haveCompany();

        /** @var User $user */
        $user = $I->haveCabinetUser([], $company);

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), []);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'code' => 404,
        ]);

        $I->seeInDatabase('company_users', ['id' => $user->getId()]);
    }

    public function testSendPostUserNotExistReturnNotFound(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'accountant',
            'password' => 'drowssaP123',
        ];

        $id = '7542c77a-5e01-4bc7-86c9-429f32c595dd';
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'code' => 404,
        ]);
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

        $user = $I->haveCabinetUser([], $myself->getCompany());

        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'accountant',
            'password' => 'drowssaP123',
        ];

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $user = $I->haveCabinetUser();

        $data = [
            'username' => uniqid(),
            'firstName' => 'John',
            'lastName' => 'Smith',
            'email' => uniqid().'@example.com',
            'role' => 'accountant',
            'password' => 'drowssaP123',
        ];

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    /*public function testSendGETReturn405(ApiTester $I)
    {
        $id = '7542c77a-5e01-4bc7-86c9-429f32c595dd';
        $I->sendGET($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 405,
        ]);
    }*/
}
