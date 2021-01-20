<?php

namespace Tests\Api\V1\Documents;

use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ListCest
{
    protected $basePath = '/api/v1/documents';

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
    public function testSendGetReturnList(ApiTester $I, Example $example)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser(['role' => $example['role']]);

        $I->authorize($user->getUserName());

        $I->haveDocument($user->getCompany()->getClient());

        $I->sendGET($this->basePath);

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

        $I->seeResponseMatchesJsonType([
            'number' => 'string',
            'file' => 'array',
            'amount' => 'float|string',
            'createdAt' => 'string',
            'type' => 'string:enum(["invoice", "act-checking"])',
            'status' => 'string:enum(["formed-automatically", "formed-by-request"])',
        ], '$.data[0]');

        $I->seeResponseMatchesJsonType([
            'name' => 'string',
            'link' => 'string',
        ], '$.data[0].file');
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

        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->sendGET($this->basePath);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
