<?php

declare(strict_types=1);

namespace Tests\Api;

use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class GenerateApiDocCest
{
    private $basePath = '/admin/api/doc/generate';

    public function testSendGETReturnSwaggerJson(ApiTester $I): void
    {
        $username = 'root';
        $password = '111';

        $I->amOnPage('/admin/sign-in');
        $I->submitForm('#loginform', [
            'username' => $username,
            'password' => $password,
        ]);

        $I->sendGET($this->basePath);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'swagger' => 'string',
            'paths' => 'array',
        ]);
    }

    public function testUnauthorizedSeeLoginPage(ApiTester $I): void
    {
        $I->amOnPage($this->basePath);

        $I->seeCurrentUrlEquals('/admin/sign-in');
    }
}
