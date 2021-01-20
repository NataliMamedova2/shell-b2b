<?php

namespace Tests\Api\V1\Drivers;

use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ListCest
{
    protected $basePath = '/api/v1/drivers';

    public function testGetListDriversReturnList(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();

        /** @var Driver $driver */
        $driver = $I->haveDriver($user->getCompany()->getClient());

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'meta' => [
                'pagination' => [
                    'totalCount' => 'integer',
                    'currentPage' => 'integer',
                ],
            ],
            'data' => 'array|empty',
        ]);

        $phones = [];
        foreach ($driver->getPhones() as $phone) {
            $phones[] = ['number' => $phone->getNumber()];
        }

        $I->seeResponseMatchesJsonType([
            'id' => 'string',
            'email' => 'string:email',
            'firstName' => 'string',
            'lastName' => 'string',
            'middleName' => 'string',
            'phones' => 'array|!empty',
            'carsNumbers' => 'array|empty',
            'status' => 'string:enum(["active", "blocked"])',
            'note' => 'string|empty',
        ], '$.data[0]');

        $I->seeResponseContainsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
            ],
            'data' => [
                [
                    'id' => $driver->getId(),
                    'firstName' => $driver->getName()->getFirstName(),
                    'phones' => $phones,
                ],
            ],
        ]);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
