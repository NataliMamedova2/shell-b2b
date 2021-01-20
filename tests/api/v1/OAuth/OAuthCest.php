<?php

namespace Tests\Api\V1\OAuth;

use App\Users\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class OAuthCest
{
    private $basePath = '/api/v1/oauth';

    public function testSendPostReturnToken(ApiTester $I)
    {
        $I->haveCabinetUser(['username' => 'admin']);

        $data = [
            'username' => 'admin',
            'password' => '111',
        ];

        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'token' => 'string',
        ]);
    }

    public function testSendPostUserNotFoundReturnError(ApiTester $I)
    {
        $data = [
            'username' => 'qwerty',
            'password' => 'qwerty',
        ];

        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'username' => 'array:!empty',
        ]);
    }

    /*public function testSendGetReturn405(ApiTester $I)
    {
        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 405,
        ]);
    }*/
}
