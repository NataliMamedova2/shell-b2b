<?php

namespace Tests\Api\V1\Employee;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ListCest
{
    protected $basePath = '/api/v1/company/employees';

    public function testSendGetReturnEmptyList(ApiTester $I)
    {
        $I->authorizeAsAdmin();

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
                'blockedCount' => 'integer',
            ],
            'data' => 'array',
        ]);

        $I->seeResponseEqualsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'totalCount' => 0,
                'activeCount' => 0,
                'blockedCount' => 0,
            ],
            'data' => [],
        ]);
    }

    public function testSendGetHaveUsersReturnList(ApiTester $I)
    {
        $myself = $I->authorizeAsAdmin();

        $company = $myself->getCompany();

        $user1 = $I->haveCabinetUser(['status' => 'active'], $company);
        $user2 = $I->haveCabinetUser(['status' => 'active'], $company);

        $user3 = $I->haveCabinetUser(['status' => 'blocked'], $company);

        $anotherUser = $I->haveCabinetUser(['status' => 'active']);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'totalCount' => 3,
                'activeCount' => 2,
                'blockedCount' => 1,
            ],
            'data' => [
                [
                    'id' => $user1->getId(),
                ],
                [
                    'id' => $user2->getId(),
                ],
                [
                    'id' => $user3->getId(),
                ],
            ],
        ]);

        $I->dontSeeResponseContainsJson([
            'data' => [
                [
                    'id' => $myself->getId(),
                ],
            ],
        ]);

        $I->dontSeeResponseContainsJson([
            'data' => [
                [
                    'id' => $anotherUser->getId(),
                ],
            ],
        ]);

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
        ], '$.data[0]');
    }

    public function testSendGetWithStatusParamReturnList(ApiTester $I)
    {
        $myself = $I->authorizeAsAdmin();

        $company = $myself->getCompany();

        $I->haveCabinetUser(['status' => 'active'], $company);
        $I->haveCabinetUser(['status' => 'active'], $company);

        $user3 = $I->haveCabinetUser(['status' => 'blocked'], $company);

        $I->sendGET($this->basePath, ['status' => 'blocked']);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'totalCount' => 3,
                'activeCount' => 2,
                'blockedCount' => 1,
            ],
            'data' => [
                [
                    'id' => $user3->getId(),
                ],
            ],
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
        $user = $I->haveCabinetUser(['role' => $example['role']]);

        $I->authorize($user->getUserName());

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }
}
