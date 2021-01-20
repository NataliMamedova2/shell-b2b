<?php

namespace Tests\Api\V1\LastSystemUpdate;

use App\Import\Domain\Import\ValueObject\Status\DoneStatus;
use App\Import\Domain\Import\ValueObject\Status\FailedStatus;
use App\Import\Domain\Import\ValueObject\Status\ProcessingStatus;
use Codeception\Util\HttpCode;
use Tests\ApiTester;
use Tests\Helper\Fixtures;

final class ReadActionCest
{
    protected $basePath = '/api/v1/last-system-update';

    /**
     * @var Fixtures
     */
    protected $fixtures;

    protected function inject(Fixtures $fixtures)
    {
        $this->fixtures = $fixtures;
    }

    public function testSendGetNoImportRecordsReturnNull(ApiTester $I)
    {
        $myself = $I->haveCabinetUser();
        $I->authorize($myself->getUserName());

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'dateTime' => 'string|null',
        ]);

        $I->seeResponseContainsJson([
            'dateTime' => null,
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function testSendGetHaveImportRecordsReturnLastSuccessfully(ApiTester $I)
    {
        $myself = $I->haveCabinetUser();
        $I->authorize($myself->getUserName());

        $I->haveInDatabase('import', [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'status' => (new DoneStatus())->getValue(),
            'started_at' => '2019-12-12 12:40:12',
            'ended_at' => '2019-12-12 12:45:10',
        ]);

        $I->haveInDatabase('import', [
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'status' => (new FailedStatus())->getValue(),
            'started_at' => '2019-12-12 13:40:12',
            'ended_at' => '2019-12-12 13:41:10',
        ]);

        $lastSuccessfullyDate = new \DateTimeImmutable('2019-12-12 14:12:10');
        $I->haveInDatabase('import', [
            'id' => '550e8400-e29b-41d4-a716-446655440011',
            'status' => (new DoneStatus())->getValue(),
            'started_at' => '2019-12-12 14:00:12',
            'ended_at' => $lastSuccessfullyDate->format('Y-m-d H:i:s'),
        ]);

        $I->haveInDatabase('import', [
            'id' => '550e8400-e29b-41d4-a716-446655440002',
            'status' => (new ProcessingStatus())->getValue(),
            'started_at' => '2019-12-12 14:40:12',
        ]);

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'dateTime' => 'string:date',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET("$this->basePath");

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
