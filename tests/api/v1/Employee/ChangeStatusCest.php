<?php

namespace Tests\Api\V1\Employee;

use App\Users\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ChangeStatusCest
{
    protected $basePath = '/api/v1/company/employees/change-status/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testSendPostChangeStatusReturnSuccess(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        /** @var \App\Clients\Domain\User\User $user */
        $user = $I->haveCabinetUser(['status' => 'blocked'], $user->getCompany());

        $data = [
            'status' => 'active',
        ];

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
        ]);

        $I->seeInDatabase('company_users', ['id' => $user->getId(), 'status' => '1']);
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
            'status' => 'array',
        ], '$.errors');
    }

    public function testSendPostUpdateMyselfReturnNotFound(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        $data = [
            'status' => 'blocked',
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
            'status' => 'active',
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
     * @throws \Exception
     *
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testForbiddenRoles(ApiTester $I, Example $example)
    {
        $authUser = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($authUser->getUserName());

        $user = $I->haveCabinetUser([], $authUser->getCompany());
        $data = [
            'status' => 'blocked',
        ];

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $user = $I->haveCabinetUser(['role' => 'admin']);
        $data = [
            'status' => 'blocked',
        ];

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    /*   public function testSendGETReturn405(ApiTester $I)
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
