<?php

declare(strict_types=1);

namespace Tests\Acceptance\Command;

use Tests\AcceptanceTester;

final class ImportCommandCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->clearTable('import');
        $I->clearTable('import_files');

        $I->copyDir('tests/_data/import/sync/1S_files', 'storage/sync/1S_files');
        $I->runShellCommand('php bin/console import:1c', false);
    }

    public function testImportTrFile(AcceptanceTester $I): void
    {
        $I->dontSeeFileFound('00116723_1C.tr', 'storage/sync/1S_files');
        $I->seeNumRecords(10, 'card_transactions', ['client_1c_id' => 'TI-0000001']);
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->clearTable('import');
        $I->clearTable('import_files');
    }
}
