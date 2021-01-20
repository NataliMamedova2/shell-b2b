<?php

namespace Tests\Api\V1\Documents;

use App\Clients\Domain\Document\Document;
use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class DowndloadCest
{
    protected $basePath = '/api/v1/documents/download/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
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
    public function testDownloadDocumentPermittedRolesReturnBinary(ApiTester $I, Example $example)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser(['role' => $example['role']]);

        $I->authorize($user->getUserName());

        /** @var Document $document */
        $document = $I->haveDocument($user->getCompany()->getClient());

        $file = $document->getFile();
        $I->seeFileFound($file->getNameWithExtension(), 'storage/source/tests');

        $I->sendGET($this->getUrl($document->getId()));

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        /** @var Document $document */
        $document = $I->haveDocument();

        $I->sendGET($this->getUrl($document->getId()));

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
