<?php

namespace Tests\Api\V1\Company;

use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class DashboardCest
{
    protected $basePath = '/api/v1/company/dashboard';

    public function testCompanyDashboardNoUsersAndDriversReturnZero(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'usersCount' => 0,
            'driversCount' => 0,
        ]);
    }

    public function testCompanyDashboardHaveOnlyActiveUsers(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->authorizeAsAdmin();
        $company = $myself->getCompany();

        for ($i = 0; $i < 10; ++$i) {
            $I->haveCabinetUser(['status' => 'active'], $company);
        }

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'usersCount' => 10,
            'driversCount' => 0,
        ]);
    }

    public function testCompanyDashboardHaveOnlyActiveDrivers(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->authorizeAsAdmin();
        $client = $myself->getCompany()->getClient();

        for ($i = 0; $i < 10; ++$i) {
            $I->haveDriver($client);
        }

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'usersCount' => 0,
            'driversCount' => 10,
        ]);
    }

    public function testSendGetHaveActiveAndBlockedUsersReturnActiveCount(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->authorizeAsAdmin();
        $company = $myself->getCompany();

        for ($i = 0; $i < 3; ++$i) {
            $I->haveCabinetUser(['status' => 'active'], $company);
        }

        for ($i = 0; $i < 4; ++$i) {
            $I->haveCabinetUser(['status' => 'blocked'], $company);
        }

        $I->sendGET("$this->basePath");

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'usersCount' => 3,
            'driversCount' => 0,
        ]);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
