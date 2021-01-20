<?php

namespace Tests\Api\V1\Drivers;

use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ChangeStatusCest
{
    protected $basePath = '/api/v1/drivers/change-status/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testSendDriverChangeStatusReturnSuccess(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();

        /** @var Driver $driver */
        $driver = $I->haveDriver($user->getCompany()->getClient());

        $data = [
            'status' => 'blocked',
        ];

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
        ]);

        $I->seeInDatabase('drivers', ['id' => $driver->getId(), 'status' => '0']);
    }

    public function testSendDriverChangeStatusEmptyDataReturnErrors(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();

        /** @var Driver $driver */
        $driver = $I->haveDriver($user->getCompany()->getClient());

        $data = [];

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'status' => 'array',
        ], '$.errors');
    }

    public function testSendDriverChangeStatusDriverAnotherCompanyReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $company = $I->haveCompany();

        /** @var Driver $driver */
        $driver = $I->haveDriver($company->getClient());

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id), []);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'code' => 404,
        ]);

        $I->seeInDatabase('drivers', ['id' => $driver->getId()]);
    }

    public function testSendDriverChangeStatusDriverNotExistReturnNotFound(ApiTester $I)
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

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $company = $I->haveCompany();

        /** @var Driver $driver */
        $driver = $I->haveDriver($company->getClient());

        $data = [
            'status' => 'blocked',
        ];

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
