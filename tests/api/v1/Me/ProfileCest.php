<?php

namespace Tests\Api\V1\Me;

use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ProfileCest
{
    protected $basePath = '/api/v1/me';

    /**
     * Allowed roles.
     *
     * @param ApiTester $I
     * @param Example $example
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testGetMeWithManagerReturnMe(ApiTester $I, Example $example)
    {
        /** @var User $myself */
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $company = $myself->getCompany();
        $client = $company->getClient();
        $manager = $I->haveManager($client);

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType(self::jsonType());
        $I->seeResponseMatchesJsonType(self::managerJsonType(), '$.manager');
        $I->seeResponseMatchesJsonType(self::companyJsonType(), '$.company');
        $I->dontSeeResponseJsonMatchesJsonPath('$.password');

        $I->seeResponseContainsJson([
            'username' => $myself->getUsername(),
            'email' => $myself->getEmail(),
            'firstName' => $myself->getName()->getFirstName(),
            'role' => $myself->getRoleName(),
            'status' => $myself->getStatusName(),
            'manager' => [
                'name' => $manager->getName(),
                'email' => $manager->getEmail(),
                'phone' => $manager->getPhone(),
            ],
            'company' => [
                'name' => $company->getName(),
                'contractNumber' => $client->getContractNumber(),
                'contractDate' => $client->getContractDate()->format('Y-m-d'),
            ],
        ]);
    }

    public function testGetMeNoManagerReturnMeManagerNull(ApiTester $I)
    {
        /** @var User $myself */
        $myself = $I->authorizeAsAdmin();

        $company = $myself->getCompany();
        $client = $company->getClient();

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();

        $I->dontSeeResponseJsonMatchesJsonPath('$.password');

        $I->seeResponseContainsJson([
            'username' => $myself->getUsername(),
            'email' => $myself->getEmail(),
            'firstName' => $myself->getName()->getFirstName(),
            'role' => $myself->getRoleName(),
            'status' => $myself->getStatusName(),
            'manager' => null,
            'company' => [
                'name' => $company->getName(),
                'contractNumber' => $client->getContractNumber(),
                'contractDate' => $client->getContractDate()->format('Y-m-d'),
            ],
        ]);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET("$this->basePath");

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    /*public function testSendPostReturn405(ApiTester $I)
    {
        $user = $I->haveCabinetUser();

        $I->authorize($user->getUsername());

        $I->sendPOST("$this->basePath");

        $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'code' => 405,
        ]);
    }*/

    private static function jsonType(): array
    {
        return [
            'username' => 'string:!empty',
            'email' => 'string:email:!empty',
            'firstName' => 'string:!empty',
            'middleName' => 'string|empty',
            'lastName' => 'string:!empty',
            'phone' => 'string|empty',
            'status' => 'string:enum(["active", "blocked"])',
            'role' => 'string:enum(["admin", "manager", "accountant"])',
            'createdAt' => 'string:date',
            'manager' => 'array',
            'company' => 'array',
        ];
    }

    private static function managerJsonType(): array
    {
        return [
            'name' => 'string:!empty',
            'phone' => 'string:!empty',
            'avatar' => 'string:!empty',
            'email' => 'string:!empty',
        ];
    }

    private static function companyJsonType(): array
    {
        return [
            'name' => 'string:!empty',
            'contractNumber' => 'string:!empty',
            'contractDate' => 'string:regex(~^(\d{4}-\d{2}-\d{2})$~)',
        ];
    }
}
