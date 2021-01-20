<?php

namespace Tests\Api\V1\Employee;

use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class DeleteCest
{
    protected $basePath = '/api/v1/company/employees/delete/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testSendPostReturnSuccess(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        /** @var User $user */
        $user = $I->haveCabinetUser([], $user->getCompany());

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);

        $I->dontSeeInDatabase('company_users', ['id' => $user->getId()]);
    }

    public function testSendPostUserAnotherCompanyReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $company = $I->haveCompany();

        /** @var User $user */
        $user = $I->haveCabinetUser([], $company);

        $id = $user->getId();
        $I->sendPOST($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'code' => 404,
        ]);

        $I->seeInDatabase('company_users', ['id' => $user->getId()]);
    }

    public function testSendPostReturnUserNotFound(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $id = '7542c77a-5e01-4bc7-86c9-429f32c595dd';
        $I->sendPOST($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
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

    public function testSendPostUnauthorizedReturnEmployer(ApiTester $I)
    {
        $id = '7542c77a-5e01-4bc7-86c9-429f32c595dd';
        $I->sendPOST($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    /* public function testSendGETReturn405(ApiTester $I)
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
