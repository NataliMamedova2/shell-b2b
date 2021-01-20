<?php

namespace Tests\Api\V1;

use App\Clients\Domain\User\User;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class PasswordRecoveryCest
{
    protected $basePath = '/api/v1/password-recovery';

    public function testSendPostEmptyDataReturnError(ApiTester $I)
    {
        $data = [];

        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'username' => 'array:!empty',
        ], '$.errors');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testSendPostUserNotFoundReturnError(ApiTester $I)
    {
        $data = [
            'username' => 'john',
        ];

        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'username' => 'array:!empty',
        ], '$.errors');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testSendPostReturnSuccess(ApiTester $I)
    {
        $I->haveCabinetUser(['username' => 'johnd']);

        $data = [
            'username' => 'johnd',
        ];

        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);
    }

    public function testSendPostCamelCaseEmailReturnSuccess(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser(['username' => 'johnd']);

        $data = [
            'username' => ucwords($user->getEmail()),
        ];

        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);
    }

    public function testSendGetReturn405(ApiTester $I)
    {
        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'code' => 405,
        ]);

        $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
    }
}
