<?php

namespace Tests\Api\V1\Drivers;

use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class SearchCest
{
    protected $basePath = '/api/v1/drivers/search';

    public function testSearchDriversReturnList(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();

        /** @var Driver $driver */
        $driver = $I->haveDriver($user->getCompany()->getClient());

        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'id' => 'string',
            'name' => 'string',
        ]);

        $I->seeResponseContainsJson([
            [
                'id' => $driver->getId(),
                'name' => $driver->getName()->getFullName(),
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
