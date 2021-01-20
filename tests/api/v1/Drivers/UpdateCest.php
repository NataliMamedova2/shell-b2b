<?php

namespace Tests\Api\V1\Drivers;

use App\Clients\Domain\Driver\Driver;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class UpdateCest
{
    protected $basePath = '/api/v1/drivers/update/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testUpdateDriverReturnDriver(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        /** @var Driver $driver */
        $driver = $I->haveDriver($user->getCompany()->getClient());

        $email = uniqid().'@example.com';
        $data = [
            'firstName' => 'Johntest',
            'lastName' => 'Smithtest',
            'middleName' => 'Doutest',
            'phones' => [
                ['number' => '+380972342344'],
                ['number' => '+380992349999'],
            ],
            'carsNumbers' => [
                ['number' => 'AA1233222'],
            ],
            'email' => $email,
            'status' => 'active',
            'note' => 'Note text',
        ];

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson($data);

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
        ]);

        $I->seeInDatabase('drivers', ['email' => $email]);
    }

    public function testUpdateDriverEmptyDataReturnErrors(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();

        /** @var Driver $driver */
        $driver = $I->haveDriver($user->getCompany()->getClient());

        $data = [];

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'firstName' => 'array',
            'lastName' => 'array',
            'phones' => 'array',
        ], '$.errors');
    }

    public function testUpdateDriverAnotherCompanyReturnError(ApiTester $I)
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

    public function testUpdateNotExistDriverReturnNotFound(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [
            'firstName' => 'John',
            'lastName' => 'Smith',
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
            'firstName' => 'John',
            'lastName' => 'Smith',
        ];

        $id = $driver->getId();
        $I->sendPOST($this->getUrl($id), $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
