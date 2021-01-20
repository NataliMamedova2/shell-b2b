<?php

namespace Tests\Api\V1\Drivers;

use App\Clients\Domain\Driver\Driver;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ReadCest
{
    protected $basePath = '/api/v1/drivers/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testReadDriverMyCompanyReturnDriver(ApiTester $I)
    {
        $myself = $I->authorizeAsAdmin();

        $company = $myself->getCompany();
        /** @var Driver $driver */
        $driver = $I->haveDriver($company->getClient());

        $I->sendGET($this->getUrl($driver->getId()));

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'email' => 'string:email',
            'firstName' => 'string',
            'lastName' => 'string',
            'middleName' => 'string',
            'phones' => 'array|!empty',
            'carsNumbers' => 'array|empty',
            'status' => 'string:enum(["active", "blocked"])',
            'note' => 'string|empty',
        ]);
    }

    public function testReadDriverAnotherCompanyReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $company = $I->haveCompany();
        /** @var Driver $driver */
        $driver = $I->haveDriver($company->getClient());

        $I->sendGET($this->getUrl($driver->getId()));

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

        $id = $driver->getId();
        $I->sendGET($this->getUrl($id));

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

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
