<?php

namespace Tests\Api\V1\Company;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ProfileUpdateCest
{
    protected $basePath = '/api/v1/company/profile/update';

    public function testSendPostReturnCompanyUpdatedInfo(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [
            'name' => 'Auchan Ukraine',
            'accountingEmail' => 'example@mail.com',
            'accountingPhone' => '+380963332211',
            'postalAddress' => '01001 м. Київ',
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson($data);
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

        $data = [
            'name' => 'Auchan Ukraine',
            'accountingEmail' => 'example@mail.com',
            'accountingPhone' => '+380963332211',
            'postalAddress' => '01001 м. Київ',
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $data = [
            'name' => 'Auchan Ukraine',
            'accountingEmail' => 'example@mail.com',
            'accountingPhone' => '+380963332211',
            'postalAddress' => '01001 м. Київ',
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    /*  public function testSendGetReturn405(ApiTester $I)
      {
          $I->authorizeAsAdmin();

          $I->sendGET($this->basePath);

          $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
          $I->seeResponseIsJson();

          $I->seeResponseContainsJson([
              'code' => 405,
          ]);
      }*/
}
