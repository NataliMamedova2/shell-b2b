<?php

namespace Tests\Api\V1\Company;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ProfileCest
{
    protected $basePath = '/api/v1/company/profile';

    public function testSendGetReturnCompanyInfo(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'name' => 'string',
            'legalName' => 'string',
            'accountingEmail' => 'string|empty',
            'accountingPhone' => 'string|empty',
            'directorEmail' => 'string:email',
            'postalAddress' => 'string|empty',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * Forbidden roles.
     *
     * @throws \Exception
     *
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testForbiddenRoles(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /* public function testSendPostReturn405(ApiTester $I)
     {
         $I->authorizeAsAdmin();

         $I->sendPOST($this->basePath);

         $I->seeResponseIsJson();
         $I->seeResponseContainsJson([
             'code' => 405,
         ]);
         $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
     }*/
}
