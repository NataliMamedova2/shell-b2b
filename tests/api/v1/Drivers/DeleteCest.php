<?php

namespace Tests\Api\V1\Drivers;

use App\Clients\Domain\Driver\Driver;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class DeleteCest
{
    protected $basePath = '/api/v1/drivers/delete/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testDeleteDriverReturnSuccess(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        /** @var Driver $driver */
        $driver = $I->haveDriver($user->getCompany()->getClient());

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id));

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);

        $I->dontSeeInDatabase('drivers', ['id' => $driver->getId()]);
    }

    public function testDeleteDriverAnotherCompanyReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $company = $I->haveCompany();

        /** @var Driver $driver */
        $driver = $I->haveDriver($company->getClient());

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'code' => 404,
        ]);

        $I->seeInDatabase('drivers', ['id' => $driver->getId()]);
    }

    public function testDeleteDriverReturnDriverNotFound(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $id = '7542c77a-5e01-4bc7-86c9-429f32c595dd';
        $I->sendPOST($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testDeleteDriverUnauthorizedReturn401(ApiTester $I)
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
}
