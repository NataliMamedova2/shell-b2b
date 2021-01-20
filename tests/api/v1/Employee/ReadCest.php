<?php

namespace Tests\Api\V1\Employee;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ReadCest
{
    protected $basePath = '/api/v1/company/employees/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testSendGetMyCompanyUserReturnUser(ApiTester $I)
    {
        $myself = $I->authorizeAsAdmin();

        $company = $myself->getCompany();
        $user1 = $I->haveCabinetUser(['status' => 'active'], $company);

        $I->sendGET($this->getUrl($user1->getId()));

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

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
    }

    public function testSendGetMyselfIdReturnError(ApiTester $I)
    {
        $myself = $I->authorizeAsAdmin();

        $I->sendGET($this->getUrl($myself->getId()));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 404,
        ]);
    }

    public function testSendGetAnotherCompanyUserIdReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $anotherUser = $I->haveCabinetUser(['status' => 'active']);
        $I->sendGET($this->getUrl($anotherUser->getId()));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 404,
        ]);
    }

    /**
     * Forbidden roles.
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

        $id = $user->getId();
        $I->sendGET($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $user = $I->haveCabinetUser();

        $id = $user->getId();
        $I->sendGET($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    /*  public function testSendPostReturn405(ApiTester $I)
      {
          $id = '7542c77a-5e01-4bc7-86c9-429f32c595dd';
          $I->sendPOST($this->getUrl($id));

          $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
          $I->seeResponseIsJson();

          $I->seeResponseContainsJson([
              'code' => 405,
          ]);
      }*/

    public function testSendPOSTEmptyIdReturn404(ApiTester $I)
    {
        $id = '';
        $I->sendPOST($this->getUrl($id));

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 404,
        ]);
    }
}
