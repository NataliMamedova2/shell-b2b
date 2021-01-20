<?php

namespace Tests\Api\V1\Documents;

use App\Clients\Domain\ShellInformation\ShellInformation;
use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ActCheckingCest
{
    protected $basePath = '/api/v1/documents/act-checking';

    /**
     * @var ShellInformation
     */
    private $shellInfo;

    public function _before(ApiTester $I)
    {
        $this->shellInfo = $I->haveShellInfo();
    }

    public function testActCheckingEmptyDataReturnErrors(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'dateFrom' => 'array:!empty',
            'dateTo' => 'array:!empty',
        ], '$.errors');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * Allowed roles.
     *
     * @param ApiTester $I
     * @param Example   $example
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     * @example { "role": "accountant" }
     */
    public function testActCheckingPermittedRolesReturnFileLink(ApiTester $I, Example $example)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser(['role' => $example['role']]);

        $client = $user->getCompany()->getClient();

        $I->authorize($user->getUserName());

        $I->haveCardTransactionList(4, [
            'client1CId' => $client->getClient1CId(),
            'postDate' => new \DateTimeImmutable('2020-01-02')
        ]);

        $I->haveCardTransactionList(2, [
            'client1CId' => $client->getClient1CId(),
            'postDate' => new \DateTimeImmutable('2020-02-02')
        ]);

        $I->haveDiscount($client);

        $data = [
            'dateFrom' => '2020-01',
            'dateTo' => '2020-02'
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'name' => 'string:!empty',
            'link' => 'string:!empty',
        ]);
    }

    /**
     * Forbidden Roles.
     *
     * @param ApiTester $I
     * @param Example $example
     *
     * @throws \Exception
     *
     * @example { "role": "manager" }
     */
    public function testForbiddenRoles(ApiTester $I, Example $example)
    {
        $user = $I->haveCabinetUser(['role' => $example['role']]);

        $I->authorize($user->getUserName());

        $I->sendPOST($this->basePath);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->sendPOST($this->basePath);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
